/**
 * Database functions
 *
 * @package E.db
 * @author Bruno Sampaio
 */

var mysql = require('mysql'), db;


/**
 * Connects to the Database
 *
 * If an error occurs prints it to the console.
 */
exports.connect = function() {
  
  // Set DB Connection
  db = mysql.createConnection(E.config.database);

  db.on('error', function(err) {
    console.error('Database connection error: '+err.code+' '+err.fatal);
    
    if (err.code === 'PROTOCOL_CONNECTION_LOST') {
      E.db.connect();
    }
  });
  
  db.on('end', function() {
    E.db.connect();
  });
}


/**
 * Set Database Query
 *
 * @param string query - the query to be executed.
 * @param function callback - the callback function.
 */
exports.query = function(query, callback) {
  
  db.query(query, function(err, results) {
    try {
      if(err) throw err;

      if(callback) callback(results);
    }
    catch(e) {
      console.log(e);
    }
  });
  
}


/**
 * Check if an item Exists
 *
 * @param string table - the table name.
 * @param string field - the field name (default : 'id').
 * @param string value - the field value.
 * @param function callback - the callback function.
 */
exports.exists = function(table, field, value, callback) {
  field = field? field : 'id';
  this.query('SELECT count('+field+') as count FROM '+table+' WHERE '+field+' = '+value+' LIMIT 1', callback);
}


/**
 * Get list of Ids from an array or object keys.
 *
 * @param mixed ids - the list of ids.
 */
exports.getIdsList = function(ids) {
  return (ids instanceof Array)? ((ids.length > 0)? ids : false) : ((ids && typeof ids === 'object')? Object.keys(ids) : false);
}


/**
 * Get a Game Resources Folder URL
 *
 * @param int gameId - the game id.
 */
exports.getResourcesFolderURL = function(gameId) {
  return E.config.urls.games + gameId + '/';
}


/**
 * Get Game Resources
 *
 * @param array ids - the resources ids list.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.getResources = function(ids, gameId, callback) {
  var folder = this.getResourcesFolderURL(gameId);
  
  var query = 
    'SELECT Resource.original_id as id, CONCAT(IF(Resource.external=0, "'+folder+'", ""), Resource.url) as url, Type.name as type, Resource.external '+
    'FROM '+
      'resources as Resource '+
      'INNER JOIN resources_types as Type ON (Type.id = Resource.type_id) '+
    'WHERE Resource.original_id IN ('+ids.join(',')+') AND Resource.game_id = '+gameId+' '+
    'ORDER BY Resource.id';
    
  this.query(query, function(results) {
    
    // Parse results
    var data = {};
    for(var i = 0; i < results.length; i++) {
      var row = results[i];
      
      data[row.id] = {
        url : row.url,
        type : row.type,
        external : row.external,
      };
      
    }
    
    callback(data);
  });
}


/**
 * Get Questions
 *
 * @param array ids - questions ids.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.getQuestions = function(ids, gameId, callback) {
  var query =
    'SELECT Activity.original_id as id, Activity.has_hints, Activity.has_resources, Question.content as question, Type.name as type, Answer.id as answerId, Answer.content as answer '+
    'FROM '+ 
      'activities as Activity '+
      'INNER JOIN questions as Question ON (Question.activity_id = Activity.id) '+
      'INNER JOIN questions_types as Type ON (Question.type_id = Type.id) '+
      'INNER JOIN answers as Answer ON (Answer.question_id = Question.id) '+
    'WHERE Activity.original_id IN ('+ids.join(',')+') AND Activity.game_id = '+gameId+' '+
    'ORDER BY Activity.original_id';
  
  this.query(query, function(results) {
    
    // Parse results
    var data = {};
    for(var i = 0; i < results.length; i++) {
      var row = results[i];
      var id = row.id;
      
      if(!(id in data)) {
        data[id] = {
          type : row.type,
          question : row.question,
          answers : {},
          helps : {
            resource: row.has_resources,
            hints : row.has_hints
          }
        };
      }
      
      data[id].answers[row.answerId] = row.answer;
    }
    
    callback(data);
  });
}


/**
 * Get Question Correct Answer
 * 
 * @param int activityId - the activity id.
 * @param int gameId - the gameId.
 * @param function callback - the callback function.
 */
exports.getAnswer = function(activityId, gameId, callback) {
  var query =
    'SELECT Answer.id, Answer.content '+
    'FROM '+
      'activities as Activity '+
      'INNER JOIN questions as Question ON (Question.activity_id = Activity.id) '+
      'INNER JOIN answers as Answer ON (Answer.question_id = Question.id) '+
    'WHERE '+
      'Activity.original_id = '+activityId+' AND '+
      'Activity.game_id = '+gameId+' AND '+
      'Answer.is_correct = 1 '+
    'LIMIT 1';
  
  this.query(query, function(results) {
    var data = (results.length > 0)? results[0] : {};
    callback(data);
  });
}


/**
 * Get Question Incorrect Answers
 *
 * @param mixed ids - the answers ids.
 * @param int activityId - the activity id.
 * @param int gameId - the gameId.
 * @param function callback - the callback function.
 * @param bool not - determines if must obtain answers with the specified ids or without.
 */
exports.getIncorrectAnswers = function(ids, activityId, gameId, callback, not) {
  ids = this.getIdsList(ids);
  
  var query =
    'SELECT Answer.id, Answer.content '+
    'FROM '+
      'activities as Activity '+
      'INNER JOIN questions as Question ON (Question.activity_id = Activity.id) '+
      'INNER JOIN answers as Answer ON (Answer.question_id = Question.id) '+
    'WHERE '+
      'Activity.original_id = '+activityId+' AND '+
      'Activity.game_id = '+gameId+' AND '+
      (ids? 'Answer.id ' + (not? 'NOT ' : ' ') + 'IN ('+ids.join(',')+') AND ' : '') +
      'Answer.is_correct = 0 '+
    'ORDER BY Answer.content';
  
  this.query(query, callback);
}


/**
 * Get Activity Hints
 *
 * @param mixed ids - the hints original ids.
 * @param int activityId - the activity id.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.getHints = function(ids, activityId, gameId, callback) {
  ids = this.getIdsList(ids);
  
  var query =
    'SELECT Hint.original_id as id, Hint.content '+
    'FROM '+
      'activities as Activity '+
      'INNER JOIN activities_hints as Hint ON (Hint.activity_id = Activity.id) '+
    'WHERE '+
      'Activity.original_id = '+activityId+' AND '+
      'Activity.game_id = '+gameId+' '+
      (ids? 'AND Hint.original_id IN ('+ids.join(',')+') ' : '')+
    'ORDER BY Hint.content';
  
  this.query(query, callback);
}


/**
 * Get Activity Resources
 *
 * @param mixed ids - the resources original ids.
 * @param int activityId - the activity id.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.getActivityResources = function(ids, activityId, gameId, callback) {
  var folder = this.getResourcesFolderURL(gameId);
  ids = this.getIdsList(ids);
  
  var query =
    'SELECT Resource.original_id as id, CONCAT(IF(Resource.external=0, "'+folder+'", ""), Resource.url) as url, Type.name as type, Resource.external '+
    'FROM '+
      'activities as Activity '+
      'INNER JOIN activities_resources as ActivityResource ON (ActivityResource.activity_id = Activity.id) '+
      'INNER JOIN resources as Resource ON (Resource.id = ActivityResource.resource_id) '+
      'INNER JOIN resources_types as Type ON (Type.id = Resource.type_id) '+
    'WHERE '+
      'Activity.original_id = '+activityId+' AND '+
      'Activity.game_id = '+gameId+' '+
      (ids? 'AND Resource.original_id IN ('+ids.join(',')+') ' : '')+
    'ORDER BY Resource.id';
  
  this.query(query, callback);
}


/**
 * Get Top 3 Sessions and the Current Session Position
 *
 * @param int score - current session score.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.getTopSessionsAndCurrentSessionPosition = function(score, gameId, callback) {
  var queries = 
    'SELECT DISTINCT score FROM sessions WHERE score != ' + score + ' AND game_id = ' + gameId + ' ORDER BY score DESC LIMIT 3; '+
    'SELECT COUNT(DISTINCT score) as position FROM sessions WHERE score > ' + score + ' AND game_id = '+gameId+' ORDER BY score DESC;';
    
  this.query(queries, function(results) {
    callback(results[0], results[1][0].position + 1);
  });
}


/**
 * Get Bonus and Scores Existing Types List
 *
 * @param function callback - the callback function.
 */
exports.getBonusAndScoresTypesList = function(callback) {
  this.query('SELECT id, name FROM bonus_types; SELECT id, name FROM scores_types;', function(results) {
    
    var bonusTypes = {}, scoresTypes = {};
    for(var i = 0; i < results[0].length; i++) {
      var bonusType = results[0][i];
      bonusTypes[bonusType.name] = bonusType.id;
    }
    
    for(var i = 0; i < results[1].length; i++) {
      var scoreType = results[1][i];
      scoresTypes[scoreType.name] = scoreType.id;
    }
    
    callback(bonusTypes, scoresTypes);
  });
}


/**
 * Create (or Insert) a Session
 *
 * @param mixed lmsId - the session lms id.
 * @param int contextId - the lms context id.
 * @param string contextName - the lms context name.
 * @param int score - the session team score.
 * @param int gameId - the game id.
 * @param function callback - the callback function.
 */
exports.createSession = function(lmsId, contextId, contextName, score, gameId, callback) {
  var self = this;
  
  // Create Insert Query
  var query =
    'INSERT INTO sessions '+
    '(lms_id, context_id, context_name, score, created, game_id) '+
    'VALUES ('+
      (lmsId? "'"+lmsId+"'" : 'NULL')+', '+
      (contextId? contextId : 'NULL')+', '+
      (contextName? "'"+contextName+"'" : 'NULL')+', '+
      (score? E.utils.roundScore(score) : 'NULL')+', '+
      'NOW(), '+
      gameId+
    ')';
  
  this.query(query, function(results) {
    callback(results.insertId);
  });
}


/**
 * Create (or Insert) a Player
 *
 * @param string name - the player name.
 * @param int helpsUsed - the number of helps used by the player.
 * @param int helpsGiven - the number of helps the player gave.
 * @param string instanceId - the instance id received from the IMS LTI request.
 * @param int userId - the lms user id.
 * @param int sessionId - the player session id.
 * @param function callback - the callback function.
 */
exports.createPlayer = function(name, helpsUsed, helpsGiven, instanceId, userId, sessionId, callback) {
  
  // Create Insert Query
  var query =
    'INSERT INTO players '+
    '(name, helps_used, helps_given, instance_id, user_id, session_id) '+
    'VALUES (\''+
      name+'\', '+
      (typeof helpsUsed === 'number'? helpsUsed : 'NULL')+', '+
      (typeof helpsGiven === 'number'? helpsGiven : 'NULL')+', '+
      (instanceId? "'"+instanceId+"'" : 'NULL')+', '+
      (userId? userId : 'NULL')+', '+
      (sessionId? sessionId : 'NULL')+
    ')';
  
  this.query(query, function(results) {
    callback(results.insertId);
  });
}


/**
 * Create (or Insert) all Player Bonus
 *
 * @param int playerId - the player id.
 * @param object list - the list of bonus by scenario.
 * @param object types - the list of bonus types.
 * @param object gameData - the game scenarios data.
 */
exports.createPlayerBonus = function(playerId, list, types, gameData) {
  var round = E.utils.roundScore;
  
  // Create Insert Query
  var query = 'INSERT INTO players_bonus (scenario, value, type_id, player_id) VALUES ', empty = true;
  for(var scenarioId in list) {
    for(var bonusType in list[scenarioId]) {
      query+= 
        '(\''+
          gameData[scenarioId].name+'\', '+
          round(list[scenarioId][bonusType])+', '+
          types[bonusType]+', '+
          playerId+
        '),';
      empty = false;
    }
  }
  
  if(!empty) this.query(query.substr(0, query.length - 1));
}


/**
 * Create (or Insert) all Player Scores
 *
 * @param int playerId - the player id.
 * @param object list - the list of player scores.
 * @param object types - the list of scores types.
 * @param object gameData - the game scores data.
 */
exports.createPlayerScores = function(playerId, list, types, gameData) {
  var round = E.utils.roundScore;
  
  // Create Insert Query
  var query = 'INSERT INTO players_scores (value, type_id, player_id) VALUES ', empty = true;
  for(var scoreType in list) {
    if(gameData[scoreType]) {
      query+= '('+round(list[scoreType])+', '+types[scoreType]+', '+playerId+'),';
      empty = false;
    }
  }
  
  if(!empty) this.query(query.substr(0, query.length - 1));
}


/**
 * Create (or Insert) all Player Activities Scores
 *
 * @param int playerId - the player id.
 * @param int gameId - the game id.
 * @param object list - the list of scores by activity.
 * @param object gameData - the game activities data.
 * @param object groupsData - the session groups data.
 */
exports.createPlayerActivities = function(playerId, gameId, list, gameData, groupsData) {
  var round = E.utils.roundScore;
  
  // Get Activities New Ids
  var self = this;
  self.query('SELECT Activity.id, Activity.original_id FROM activities as Activity WHERE Activity.game_id = '+gameId, function(results) {
    
    // Map old ids to new ids
    var activitiesIds = {};
    for(var i = 0; i < results.length; i++) {
      activitiesIds[results[i].original_id] = results[i].id;
    }
    
    var query = 'INSERT INTO activities_logs (reward, penalty, attempts, player_id, activity_id) VALUES ';
    var empty = true;
    
    // Create Insert Query
    for(var activityId in list) {
      var activity = gameData[activityId];
      var activityScores = activity.scores;
      var log = list[activityId];

      var activityId = activitiesIds[activity.group? groupsData[activity.source] : activity.source];
      if(activityScores.reward.log || activityScores.penalty.log) {
        query+= 
          '('+(activityScores.reward.log? round(log.reward) : 'NULL')+', '+
          (activityScores.penalty.log? round(log.penalty) : 'NULL')+', '+
          log.attempts+', '+
          playerId+', '+
          activityId+
        '),';
        empty = false;
      }
    }

    if(!empty) self.query(query.substr(0, query.length - 1));
  });
}

