

/**
 * Player Constructor
 *
 * @param string name - the player name.
 * @param string avatar - the player avatar url.
 * @param int helps - player helps.
 * @param string sessionId - the session id.
 * @param object socket - the client socket.
 * @param string instance - the instance id received from the IMS LTI standard.
 * @param int userId - the lms user id.
 */
exports.Player = function(name, avatar, helps, sessionId, socket, instance, userId) {
  
  // Set Player Info
  this.name = name;
  this.avatar = avatar;
  
  // Set Game Data
  this.ready = false; // Is Ready to Play
  this.scores = {}; // Player Scores
  this.helps = { 
    used: 0, // Helps Used on Current Scenario
    given: 0,
    total: helps // Player Helps
  };
  this.helping = false; // Player is Helping other Player
  this.beingHelped = false; // Player is Being Helped
  this.finished = { length : 0 }; // Finished Activities
  this.logs = { 
    bonus : {}, // Bonus Received on each Scenario
    activities : {}, // Points Received for each Activity
  };
  this.scenario = 0;
  
  // Set Client and Session Data
  this.sessionId = sessionId;
  this.socket = socket;
  
  // Set LMS Data
  this.instance = instance;
  this.userId = userId;
}

exports.Player.prototype = {

  
  setScenario : function(scenario) {
    this.scenario = scenario;
  },

  getScenario : function(scenario) {
    return this.scenario;
  },

  /**
   * Get Player Info for Presentation
   */
  getInfo : function() {
    var info = { id: this.socket.id, name: this.name, image: this.avatar };
    delete this.avatar;
    return info;
  },

  setScores : function(score, value) {
    this.scores[score] = value;
  },

  getScores : function() {
    return this.scores;
  },

    /**
   * Set Player Status to Ready and Start Session
   */
  setReady : function() {
    this.ready = true;
    var session = this.getSession();
    if(session) session.start();  
  },

   /**
   * Get Player Session
   *
   * @return Session
   */
  getSession : function() {
    return E.sessions.get(this.sessionId);
  },

  getSocket : function() {
    return this.socket;
  },

  getManager : function() {
   return E.games_manager.getManager();
  },

  updateHelps : function() {
    this.helps.used++;
    this.helps.total--;
  },

  getTotalHelps : function() {
    return this.helps.total;
  },

  /**
   * Get Helps Given
   *
   * @return int
   */
  getHelpsGiven : function() {
    return this.helps.given;
  },

  /**
   * Set Being Helped Property
   *
   * @param string playerId - the chosen player id to help.
   * @param string activityId - the activity id on which a help was requested.
   * @param string helpType - the type of help requested.
   */
  setBeingHelped : function(playerId, activityId, helpType) {
    this.beingHelped = { playerId : playerId, activityId : activityId, helpType : helpType };
  },

  /**
   * Set Helping Property
   *
   * @param string playerId - the player id that requested help.
   * @param string activityId - the activity id on which a help was requested.
   */
  setHelping : function(playerId, activityId) {
    this.helping = { playerId : playerId, activityId : activityId };
  },

  getHelping : function() {
    return this.helping;
  },

  getBeingHelped : function() {
    return this.beingHelped;
  },

  incHelpsGiven : function() {
    this.helps.given++;
  },

  /**
   * Check if Player has Solved the specified Activity
   *
   * @param string id - the activity id.
   * @return bool
   */
  hasSolvedActivity : function(id) {
    return typeof this.finished[id] !== 'undefined' && this.finished[id];
  },

  resetVariables : function() {
    this.helps.used = 0;
    this.helping = false;
    this.beingHelped = false;
    this.finished = { length : 0 };
  },

  /**
   * Get Activity Log
   * 
   * If there is no log for the specified activity, it creates its log.
   * 
   * @param string id - the activity id.
   * @return object
   */
  getActivityLog : function(id) {
    var activitiesLog = this.logs.activities;
    if(typeof activitiesLog[id] === 'undefined') {
      activitiesLog[id] = { reward : 0, penalty : 0, attempts : 0, helps : {} };
    }
    return activitiesLog[id];
  },

  /**
   * Get Total Score
   *
   * @return double
   */
  getTotalScore : function() {
    return this.scores.total;
  },

  /**
   * Set Player Penalty Score
   *
   * @param Session session - the session object.
   * @param object activityScores - the activity data.
   * @param object obtainedPoints - the activity log.
   * @param object send - the data to send to the client.
   * @param int multiply - the penalty will be multiplied by this value (default: 1).
   */
  setPenalty : function(session, activityScores, obtainedPoints, send, multiply) {
    if(!multiply) multiply = 1;
    
    // Calculate Penalty
    var penalty = ((activityScores.reward.value - obtainedPoints.penalty) * (activityScores.penalty.value / 100)) * multiply;
    obtainedPoints.penalty+= penalty;
    this.scores.penalty+= penalty;
    this.scores.total-= penalty;
    
    // Remove From Team Score
    var manager = session.getManager();
    if (manager)
      manager.updateScore(-penalty);
    
    // Set Data to Send
    var playerScoresToSend = send.players[this.socket.id];
    playerScoresToSend.penalty = E.utils.roundScore(this.scores.penalty);
    playerScoresToSend.activityPoints = E.utils.roundScore(activityScores.reward.value - obtainedPoints.penalty);
  },

  /**
   * Set Player Reward Score
   *
   * @param Session session - the session object.
   * @param Game game - the game object.
   * @param object activityScores - the activity data.
   * @param object obtainedPoints - the activity log.
   * @param object send - the data to send to the client.
   * @param array giveBonusTo - the array of players that must receive a bonus.
   */
  setReward : function(session, game, activityScores, obtainedPoints, send, giveBonusTo) {
    var manager = session.getManager();
    // Set Activity as Finished
    if (manager) {

      var currentScenario = this.scenario;
      var playerScoresToSend = send.players[this.socket.id];
      var round = E.utils.roundScore;
      

      // Calculate Reward
      obtainedPoints.reward = activityScores.reward.value;
      this.scores.reward+= obtainedPoints.reward;
      this.scores.total+= obtainedPoints.reward;
      
      // If player is being helped on this activity
      if(this.beingHelped && this.beingHelped.activityId == playerScoresToSend.activityId) {
        var player = E.players.get(this.beingHelped.playerId);
        if(player) player.cancelHelpRequest();
        
        if(this.beingHelped.helpType in obtainedPoints.helps) {
          delete obtainedPoints.helps[this.beingHelped.helpType];
        }
        this.beingHelped = false;
      }
      
        this.setActivityAsFinished(playerScoresToSend.activityId);    
      
        // If All Activities Finished
        var bonus = 0, allFinished = this.finished.length == manager.getScenarioActivitiesCount(currentScenario);
        
        if(allFinished) {
          var howManyFinished = session.getHowManyFinishedActivities();
          
          // Set First to Finish Bonus
          var bonusType = 'firstToFinish', bonusData;
          if(howManyFinished == 0 && (bonusData = manager.getScenarioBonus(currentScenario, bonusType))) {
            this.setBonus(currentScenario, bonusType, bonusData, 'reward');
            bonus = bonusData.value;
          }
          
          session.addPlayerWhoFinishedActivities();
          
          playerScoresToSend.finished = manager.hasJump(currentScenario, 'allFinished');
        }
      }
      
      // If there was Collaborations
      if((giveBonusTo instanceof Array) && giveBonusTo.length > 0) {
        
        var bonusType = 'collaboration', bonusData;
        if(bonusData = manager.getScenarioBonus(currentScenario, bonusType)) {
          
          // Give collaboration bonus to other players
          for(var i = 0; i < giveBonusTo.length; i++) {
            var player = E.games_manager.getPlayer(giveBonusTo[i]);
            if(player) {
              player.setBonus(currentScenario, bonusType, bonusData, bonusType);
              bonus+= bonusData.value;
              
              // Set other player scores to send
              var otherScoresToSend;
              if(typeof send.players[player.socket.id] === 'undefined') {
                otherScoresToSend = send.players[player.socket.id] = {};
              }
              otherScoresToSend[bonusType] = round(player.scores[bonusType]);
              otherScoresToSend.total = round(player.scores.total);
            }
          }
        }
      }
      
      // Add to Team Score
      manager.updateScore(obtainedPoints.reward + bonus);
      
      // Set Data to Send
      playerScoresToSend.reward = round(this.scores.reward);
  },

  /**
   * Set Player Bonus
   *
   * @param string scenarioId - the scenario id.
   * @param string type - the bonus type.
   * @param object data - the bonus data.
   * @param string scoreType - the score type.
   */
  setBonus : function(scenarioId, type, data, scoreType) {
    var bonus = data.value;
    this.scores[scoreType]+= bonus;
    this.scores.total+= bonus;
    
    if(data.log) {
      if(typeof this.logs.bonus[scenarioId] === 'undefined') {
        this.logs.bonus[scenarioId] = {};
      }
      
      var bonusLog = this.logs.bonus[scenarioId];
      if(typeof bonusLog[type] === 'undefined') {
        bonusLog[type] = 0;
      }
      
      bonusLog[type]+= bonus;
    }
  },

  /**
   * Set Activity as Finished
   *
   * @param string id - the activity id.
   */
  setActivityAsFinished : function(id) {
    this.finished[id] = true;
    this.finished.length++;
  },

  handleDisconnect : function() {
    if(typeof this.sessionId !== 'undefined') {
      var session = this.getSession();
      if(session) {
        var manager = session.getManager();
        if (manager) manager.removePlayer(this.socket.id, session);
      }
    }
  }

};

/**
 * Add Player to List and Session
 *
 * @param object socket - the client socket.
 * @param object data - the data sent by the player.
 */
exports.register = function(socket, gameHelps, data, sessionId, callback) {
  var player = new this.Player(data.name, data.avatar, gameHelps, sessionId, socket, data.instance, data.userId);
  if (player)
    callback(player);
  else
    console.error('Invalid Player Register');
}
