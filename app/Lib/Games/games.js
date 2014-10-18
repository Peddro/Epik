
var extendedGames_list = {};

/**
 * Game Constructor
 *
 * @param int id - the game id.
 * @param object data - the game data.
 */
exports.Game = function(id, genreCode, extendedGameId, data) {
  this.id = id;
  this.extendedGameId = extendedGameId;
  this.genreCode = genreCode;

  // Set Game Data
  for(var i in data) {
    this[i] = data[i];
  }
  
}

exports.Game.prototype = {

  /**
   * Creates the to Load List
   *
   * This method gets the game resources and activities data from the database
   * to be sent to the players.
   *
   * @param object groupsData - the session groups data (just if it was already started by other player).
   * @param function callback - the callback function.
   */
  getToLoadList : function(groupsData, callback) {
    var toLoad = { resources : {}, activities : {} };
    var activities = this.load.activities;
    var loading = 0, loaded = 0;
    
    // Count the type of activities to Load
    for(var activityType in activities) {
      loading++;
    }
    
    // Get Resources Data
    var resourcesIds = this.load.resources;
    if(typeof resourcesIds != 'undefined' && resourcesIds.length > 0) {
      loading++;
      
      E.db.getResources(resourcesIds, this.id, function(resourcesData) {
        toLoad.resources = resourcesData;
        loaded++;
        
        if(loading == loaded) {
          callback(toLoad);
        }
      });
    }

    // Get Activities Data
    if('question' in activities) {
      var questionsIds = activities.question[0];
      toLoad.activities.question = [];

      // Set Groups Question
      if(groupsData) {
        toLoad.activities.question[1] = groupsData;
      }
      else if(activities.question.length > 1) {
        toLoad.activities.question[1] = {};

        var groups = activities.question[1];
        for(var i in groups) {
          var group = groups[i];
          var groupQuestion = group[Math.round(Math.random() * (group.length-1))];

          questionsIds.push(groupQuestion);
          toLoad.activities.question[1][i] = groupQuestion; 
        }
      }

      // Get Questions Data
      if(questionsIds.length > 0) {
        E.db.getQuestions(questionsIds, this.id, function(questionsData) {
          toLoad.activities.question[0] = questionsData;
          loaded++;
          
          if(loading == loaded) {
            callback(toLoad);
          }
        });
      }
    }
  },

  isSync : function() {
    if (this.properties.sync.value == 1)
      return true;
    return false;
  },

  /**
   * Get Activities Collection
   * @return object
   */
  getActivities : function() {
    return this.activities;
  },

  /**
   * Get the Players Property Data
   * @return object
   */
  getPlayers : function() {
    return this.properties.players;
  },

  /**
   * Get the Helps Property Data
   * @return object
   */
  getHelps : function() {
    return this.properties.helps;
  },

  /**
   * Get the Scores Property Data
   * @return object
   */
  getScores : function() {
    return this.properties.scores;
  },

  isMultiplayer : function() {
    return this.genreCode.indexOf('Collaborative') > -1;
  },

  /**
   * Get the Scenarios Collection
   * @return object
   */
  getScenarios : function() {
    return this.scenarios;
  },

  /**
   * Check if Scenario has a Jump with the specified type and with the specified 'on' property.
   *
   * @param string scenarioId - the scenario id.
   * @param string type - the jump type.
   * @param string on - the jump on attribute (optional).
   * @return bool
   */
  hasJump : function(scenarioId, type, on) {
    if(this.hasScenario(scenarioId)) {
      var jumps = this.scenarios[scenarioId].jumps;
      if(type in jumps) {
        if(!on || (on && jumps[type].on == on)) {
          return true;
        }
      }
    }
    return false;
  },

  /**
   * Get a Scenario Helps and Timeout Data
   *
   * @param string scenarioId - the scenario id.
   * @return object
   */
  getScenarioHelpsAndTimeout : function(scenarioId) {
    var data = {};
    if(this.hasScenario(scenarioId)) {
      
      // Set Scenario Helps
      var rules = this.scenarios[scenarioId].rules;
      if(typeof rules !== 'undefined' && typeof rules.helps !== 'undefined') {
        data.helps = rules.helps;
      }
      
      // Set Scenario Timeout
      var jumps = this.scenarios[scenarioId].jumps;
      if(typeof jumps.timeout !== 'undefined') {
        data.timeout = jumps.timeout.on;
      }
    }
    return data;
  },

  getID : function() {
    return this.id;
  },

  getExtendedGame : function() {
    if (this.genreCode.indexOf("Quiz") > -1)
     return E.quiz.get(this.extendedGameId);

   return null;
   console.error("Invalid Extended Game");
  },

  /**
   * Get a Scenario Jump To Data
   *
   * @param string scenarioId - the scenario id.
   * @param string type - the jump type.
   * @return string
   */
  getJumpTo : function(scenarioId, type) {
    if(this.hasScenario(scenarioId)) {
      var jumps = this.scenarios[scenarioId].jumps;
      if(type in jumps) {
        return jumps[type].to;
      }
    }
    return false;
  },

  addJump : function(playerSessions, jumpType) {
    var extendedGame = this.getExtendedGame();
    var sync = this.isSync();
    
    if (extendedGame) 
      extendedGame.addJump(playerSessions, jumpType, sync);
    
    return null;
  },

  /**
   * Get Scenario Activities Count
   *
   * @param string scenarioId - the scenario id.
   * @return int
   */
  getScenarioActivitiesCount : function(scenarioId) {
    if(this.hasScenario(scenarioId)) {
      return this.scenarios[scenarioId].activities;
    }
    return false;
  },

  /**
   * Get a Scenario Bonus Data
   *
   * @param string scenarioId - the scenario id.
   * @param string type - the bonus type.
   * @return object
   */
  getScenarioBonus : function(scenarioId, type) {
    var rules = this.getScenarioRules(scenarioId);
    if(rules && typeof rules !== 'undefined' && typeof rules.bonus !== 'undefined' && (type in rules.bonus)) {
      return rules.bonus[type];
    }
    return false;
  },

  /**
   * Get Scenario Rules Data
   *
   * @param string scenarioId - the scenario id.
   * @return object
   */
  getScenarioRules : function(scenarioId) {
    if(this.hasScenario(scenarioId)) {
      return this.scenarios[scenarioId].rules;
    }
    return false;
  },

  /**
   * Check if Game has the specified Scenario
   *
   * @param string scenarioId - the scenario id.
   * @return bool
   */
  hasScenario : function(scenarioId) {
    return scenarioId in this.scenarios;
  },

  /**
   * Get Activity in Scenario
   *
   * @param string scenarioId - the scenario id.
   * @param string id - the activity id.
   * @return object
   */
  getActivity : function(scenarioId, id) {
    if(this.hasScenario(scenarioId)) {
      var contents = this.scenarios[scenarioId].contents;
      if((id in contents) && (id in this.activities)) {
        return this.activities[id];
      }
    }
    return false;
  },

  /**
   * Get Activity Help
   *
   * @param string scenarioId - the scenario id.
   * @param string id - the activity id.
   * @param string type - the help type.
   * @return object
   */
  getActivityHelp : function(scenarioId, id, type) {
    var activity = this.getActivity(scenarioId, id);
    if(activity && typeof activity.helps !== 'undefined' && (type in activity.helps)) {
      return activity.helps[type];
    }
    return false;
  },

  /**
   * Get Scenario Helps Data
   *
   * @param string scenarioId - the scenario id.
   * @return int
   */
  getScenarioHelps : function(scenarioId) {
    var rules = this.getScenarioRules(scenarioId);
    if(rules && typeof rules !== 'undefined' && typeof rules.helps !== 'undefined') {
      return rules.helps;
    }
    return false;
  },

  useHelp : function(data, callback) {
    var extendedGame = this.getExtendedGame();
    if (extendedGame) 
      extendedGame.useHelp(data, function(request) {
        callback(request);
      });
  },

  /**
   * Get Activity Collaboration Timeout
   *
   * @param string scenarioId - the scenario id.
   * @param string id - the activity id.
   * @return int
   */
  getActivityHelpsTimeout : function(scenarioId, id) {
    var activity = this.getActivity(scenarioId, id);
    if(activity && typeof activity.scores.timeout !== 'undefined' && activity.scores.timeout.value > 0) {
      return activity.scores.timeout.value;
    }
    return false;
  },

  remove : function() {
    var extendedGame = this.getExtendedGame();
    if (extendedGame){
        extendedGame.remove();
    }
    E.games.removeExtendedGame(this.extendedGameId); 
  }

};

/**
 * Load Game Data
 *
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.load = function(gameId, genreCode, callback) {
  var exists = E.games_manager.checkGame(gameId);
  if(exists) {
    var game = E.games_manager.getGame(gameId);
    callback(game);
  }
  else {
    // Check if Game Exists
    var self = this;

    E.db.exists('games', 'id', gameId, function(results) {
      var path = E.config.folders.games + gameId + '/server.json';
      
      // If Game and Server File Exists
      if(results.length > 0 && results[0].count > 0 && E.files.existsSync(path)) {
        
        var extendedGameId = new Date().getTime();
        // Load and Store Game Data
        var game = new self.Game(gameId, genreCode, extendedGameId, JSON.parse(E.files.readFileSync(path, 'utf8')));
        
        if (genreCode.indexOf("Quiz") > -1) {
          E.quiz.load(extendedGameId, gameId, genreCode, function(quiz) {
            extendedGames_list[extendedGameId] = quiz;
            callback(game);
          });
        }
      }
      else {
        callback(false);
      }
    });
  }
}

exports.getExtendedGame = function(id) {
  if(id in extendedGames_list) {
    return extendedGames_list[id];
  }
  console.error('Invalid Extended Game');
  return null;  
}

exports.removeExtendedGame = function(id) {
  if(id in extendedGames_list) {
    delete extendedGames_list[id];
  }
}