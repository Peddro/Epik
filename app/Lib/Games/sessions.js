
var list = {}, waiting = {}; // List of open session and waiting sessions, respectively
var crypto = require('crypto');

exports.Session = function(lms, contextId, managerId) {
  
  // Set Id
  var hash = crypto.createHash('md5');
  hash.update((String) ((new Date()).getTime()));
  this.id = hash.digest('hex');
  
  // Set LMS Data
  this.lms = lms;
  this.context = { id : contextId, name : 0 };
  this.managerId = managerId;

  // Set Game Session Data
  this.players = []; // Session Players
  this.groups = false; // Questions chosen from groups
  this.howManyFinishedActivities = 0; // Number of Player that Finished all Activities on Current Scenario
  this.howManyFinishedGame = 0; //Number of Playeres that Finished the Game

  this.flow = {};

  // Increment Game Sessions Count
  var manager = this.getManager();
  if(manager) manager.addSession();
}

exports.Session.prototype = {

  /**
   * Start Session
   */
  start : function() {
    var manager = this.getManager();
    var game = manager.getGame();

    if(game && this.players.length == game.getPlayers().max) {

      // Set Players Data to be Sent
      var i = 0, allReady = true, playersInfo = [];
      while(i < this.players.length && allReady) {
        var player = this.getPlayer(i);
        if(allReady = (player && player.ready)) {
          playersInfo.push(player.getInfo());
        }
        i++;
      }
      
      // If all players are ready
      if(allReady) {
        // Trigger Start for each Player
        var send = game.getScenarioHelpsAndTimeout(game.start);
        send.scenarioId = game.start;
        this.forEachPlayer(function(player) {

          // Set Player Current Scenario
          player.setScenario(game.start);
          send.players = playersInfo;
          manager.startGame(send, player.getSocket());
          playersInfo.push(playersInfo.splice(0, 1)[0]);
        });
      }

    }
  },

  setManagerId : function(managerId) {
    this.managerId = managerId;
  },

  resetFlow : function() {
    this.flow = {};
  },

  getFlow : function() {
    return this.flow;
  },

  getLMS : function() {
    return this.lms;
  },

  getContext : function() {
    return this.context;
  },

    /**
   * Iterate over each Player
   *
   * @param function callback - the callback function to be executed for each player.
   * @param string notId - if specified this id will be ignored.
   */
  forEachPlayer : function(callback, notId) {
    for(var i = 0; i < this.players.length; i++) {
      if(!notId || notId != this.players[i]) {
        var player = this.getPlayer(i);
        if(player) callback(player);
      }
    }
  },

  /**
   * Get a Player from this Session
   *
   * @param int index - the players array index.
   * @return Player
   */
  getPlayer : function(index) {
    return E.games_manager.getPlayer(this.players[index], "Session Calling For Each");
  },

  getPlayers : function() {
    return this.players;
  },

  /**
   * Add Player
   *
   * @param string playerId - the player id.
   * @param int max - the maximum number of players.
   * @return bool
   */
  addPlayer : function(playerId, max) {
    var result = false;

    // Add Player to Session if not Full
    if(this.players.length < max) {
      this.players.push(playerId);
      result = true;
    }

    // If session is full remove it from waiting
    if(result && this.players.length == max) {
      E.sessions.removeWaiting(this.lms, this.context.id, this.managerId);
    }

    return result;
  },

  /**
   * Set Session Context Name
   *
   * @param string value - the new name.
   */
  setContextName : function(value) {
    this.context.name = value;
  },

  /**
   * Set Groups Data
   *
   * @param object data - the new data.
   */
  setGroups : function(data) {
    this.groups = data;
  },


  /**
  */
  getManager : function() {
    return E.games_manager.getManager(this.managerId);  
  },
  
  /**
   * Get Groups Data
   *
   * @return object
   */
  getGroups : function() {
    return this.groups;
  },

  /**
   * Get Group Activity Source
   *
   * @param string id - the group activity id.
   * @return int
   */
  getGroupSource : function(id) {
    if(this.groups && (id in this.groups)) {
      return this.groups[id];
    }
    return null;
  },

  /**
   * Get how many Players Finished all Activities on current Scenario
   *
   * @return int
   */
  getHowManyFinishedActivities : function() {
    return this.howManyFinishedActivities;
  },

  /**
   * Add Player who Finished all Activities on current Scenario
   */
  addPlayerWhoFinishedActivities : function() {
    this.howManyFinishedActivities++;
  },

  howManyPlayersFinishedGame : function() {
    return this.howManyFinishedGame;
  },

  addPlayerWhoFinishedGame : function() {
    this.howManyFinishedGame++;
  },

  /**
   * Add Jump
   *
   * @param string playerId - the player id that is ready to jump.
   * @param string jumpType - the jump type.
   */
  addJump : function(jumpType) {
    if(!(jumpType in this.flow)) this.flow[jumpType] = 0;
    // Increment Clicks Number
    if(this.flow[jumpType] >= 0 && this.flow[jumpType] < this.players.length) {
      this.flow[jumpType]++;
    }
  },

  /**
   * Verifies if must Jump to next Scenario
   *
   * @param string jumptType - the jump type.
   * @return bool
   */
  mustJump : function(jumpType, sync) {
    if (sync) {
      var jumpTo = (this.flow[jumpType] == this.players.length)? jumpType : false;
      
      if(!jumpTo) {
        var sumTo = (jumpType == 'allFinished')? 'skip' : ((jumpType == 'skip')? 'allFinished' : false);
        if(sumTo && (this.flow[sumTo] + this.flow[jumpType] >= this.players.length)) {
          jumpTo = 'skip';
        }
      }
      return jumpTo;
    }
    else {
      return jumpType;
    }    
  },

};

/**
 * Get Session from List
 *
 * @param int id - the session id.
 * @return Session
 */
exports.get = function(id) {
  if(id in list) {
    return list[id];
  }
  console.error('Invalid Session');
  return null;
}

/**
 * Get Session from Waiting List
 *
 * If there are no sessions creates a new one.
 *
 * @param int lms - the session LMS id.
 * @param int context - the session LMS context.
 * @param int gameId - the session game id.
 * @return Session
 */
exports.getWaiting = function(lms, context, managerId) {
  
  // Create Session Group
  if(typeof waiting[lms] === 'undefined') waiting[lms] = { length : 0 };
  if(typeof waiting[lms][context] === 'undefined') {
    waiting[lms][context] = { length : 0 };
    waiting[lms].length++;
  }
  if(typeof waiting[lms][context][managerId] === 'undefined') {
    waiting[lms][context][managerId] = [];
    waiting[lms][context].length++;
  }
  
  // Create or Get Session
  var group = waiting[lms][context][managerId];
  if(group.length == 0) {
    var session = new this.Session(lms, context, managerId);
    group.push(session);
    list[session.id] = session;
  }
  
  return group[0];
}

/**
 * Remove Session from Waiting List
 *
 * @param int lms - the session LMS id.
 * @param int context - the session LMS context.
 * @param int gameId - the session game id.
 * @return bool
 */
exports.removeWaiting = function(lms, context, managerId) {
  var removed = false;
  
  if(lms in waiting && context in waiting[lms] && managerId in waiting[lms][context]) {
    
    // Remove Session
    var group = waiting[lms][context][managerId];
    if(group.length > 0) {
      if(group.splice(0, 1).length > 0) removed = true;
    }

    // Remove Game Sessions List
    if(group.length == 0) {
      delete waiting[lms][context][managerId];
      waiting[lms][context].length--;
    }

    // Remove LMS Context Games List
    if(waiting[lms][context].length == 0) {
      delete waiting[lms][context];
      waiting[lms].length--;
    }

    // Remove LMS Contexts List
    if(waiting[lms].length == 0) {
      delete waiting[lms];
    }
  }
  
  return removed;
}

/**
 * Remove Session from List
 *
 * @param int id - the session id.
 */
exports.remove = function(id) {
  if(id in list) {
    delete list[id];
  }
}