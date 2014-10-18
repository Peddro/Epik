
var games_list = {}; // List of games with open sessions
var players_list = {}; // List of players with open sessions
var manager_list = {}; // List of Game Managers
var session_to_manager = {};

/**
 * Game Constructor
 *
 * @param int id - the game id.
 * @param object data - the game data.
 */
exports.Games_Manager = function(gameId, managerId) {

  this.id = managerId;
  this.gameId = gameId;
  this.sessions = 0;
  this.removed = false;

  this.score = 0; // Team score

}

exports.Games_Manager.prototype = {

  /**
   * Get Session Game
   *
   * @return Game
   */
  getGame : function() {
    return E.games_manager.getGame(this.gameId);
  },

  setSessionId : function(sessionId) {
    this.sessionId = sessionId;
  },

  getScenarioBonus : function(scenario, bonusType) {
    var game = this.getGame();
    if (game) {
      return game.getScenarioBonus(scenario, bonusType);
    }
    return null;
  },

  /**
   * Send Game Start Information
   *
   * @param object data - the data to send to the client. 
   */
  startGame : function(data, socket) {
    // data = { scenarioId : string, players : [{ id : string, name : string, image : string }], helps : int, timeout : int };
    socket.emit('start game', data);
  },

  /**
   * Send Data to Load
   *
   * @param object data - the data to send to the client. 
   */
  startLoading : function(data, socket) {
    // data = { activities : {}, resources : {} };
    socket.emit('start loading', data);
  },

  /**
   * Send Player Updated Status
   *
   * @param object data - the data to send to the client. 
   */
  updateStatus : function(data, socket) {
    // data = { playerId : string, status : string };
    socket.emit('update status', data);
  },

  clickButton : function(data, session, playerId, scenario) { 
    if(data && typeof data.buttonId === 'string' && typeof data.buttonType === 'string') {  
      var self = this;  

      // Get Game and Verify if it has the specified Jump
      var game = this.getGame();
      if(game && game.hasJump(scenario, data.buttonType, data.buttonId)) {

        if (game.isSync())
          session.addJump(data.buttonType);

        // Notify Status to other Players
        var send = { playerId : playerId, status : data.buttonType };
        session.forEachPlayer(function(player) {
          self.updateStatus(send, player.getSocket());
        }, playerId);

        // Switch Scenario
        self.setCurrentScenario(data.buttonType, session, playerId, scenario);
        
      }
    }
  },

  hasJump : function(scenario, type) {
    var game = this.getGame();
    if (game) 
      return game.hasJump(scenario, type);

    return null;
  },

  /**
   * Add Session to Counter
   */
  addSession : function() {
    this.sessions++;
  },

  /**
   * Remove Session from Counter
   */
  removeSession : function() {
    this.sessions--;

    if(this.sessions < 1) {
      E.games_manager.removeGame(this.gameId);
    }
  },

  /**
   * Set Current Scenario
   * 
   * @return bool
   */
  setCurrentScenario : function(jump, session, playerId, prevScenario) {
    var manager = this;
    var game = this.getGame();
    var currentPlayer = E.games_manager.getPlayer(playerId, "currentScenario");

    if(game) {
      if(jump = session.mustJump(jump, game.isSync())) {

        setTimeout(function() {

          // Get Scenario
          var scenarioId = game.getJumpTo(prevScenario, jump);

          if(scenarioId) {

            // Switch Scenario
            session.howManyFinishedActivities = 0;
            var send = game.getScenarioHelpsAndTimeout(scenarioId);
            send.scenarioId = scenarioId;
            
            if (game.isSync()) {
              session.resetFlow();

              // Notify Players             
              session.forEachPlayer(function(player) {
                player.setScenario(scenarioId);
                manager.switchScenario(send, player);                
              });
            }

            // Game not Sync
            else {

              // Switch Scenario
              manager.switchScenario(send, currentPlayer);
              currentPlayer.setScenario(scenarioId);
            }            
          }
          else {
            if (!game.isSync()) {
              session.addPlayerWhoFinishedGame();

              do {
                  //nothing, wait for others to finish
              } while(session.howManyPlayersFinishedGame() < session.getPlayers().length);
            }
            manager.finish(true, session);
          }
        }, 300);
        
        return true;
      }
    }
    
    return false;
  },

  /**
   * Reset Player Attributes and Send the Next Scenario Data
   *
   * @param object data - the data to send to the client. 
   */
  switchScenario : function(data, player) {
    var game = this.getGame();
    if (game.isSync()) player.resetVariables();
    var socket = player.getSocket();
    
    // data = { scenarioId : string, helps : int, timeout : int };
    socket.emit('switch scenario', data);
  },

 getPlayers : function() {
    return players_list;
  },

  /**
   * Update Team Score
   *
   * @param double value - the value to increment to the team score.
   */
  updateScore : function(value) {
    this.score+= value;
  },

  getScore : function() {
    return this.score;
  },

  answerQuestion : function(data, session, finished, playerId, scenario) {

    if(data && typeof data.activityId === 'string' && typeof data.answer !== 'undefined' && typeof data.helps !== 'undefined' && typeof finished[data.activityId] === 'undefined') { 
      // Get Game and Verify if it has the specified Question
      // Get Game Type (Quiz, Puzzle, ...) and subs E.quiz... E.puzzle... etc
      var game = this.getGame(), currentScenario = scenario, question;
      if(game && (question = game.getActivity(currentScenario, data.activityId))) {

        // Get Question Source
        var source = question.group? session.getGroupSource(question.source) : question.source;
        if(source) {

          var self = this;
          var player = E.games_manager.getPlayer(playerId, "Answer Question");

          E.db.getAnswer(source, game.id, function(answerData) {
            if(typeof answerData.id != 'undefined' && typeof answerData.content != 'undefined') {
              var send = { players : {} };
              send.players[playerId] = {activityId : data.activityId, solution : data.answer, finished : false };

              // Check if Answer is Correct
              var isCorrect = false, allFinished = false;
              if(typeof data.answer === 'number') {
                isCorrect = answerData.id == data.answer;
              }
              else {
                isCorrect = answerData.content.toLowerCase() == data.answer.toLowerCase();
              }

              // Get Player Points obtained with this Question
              var obtainedPoints = player.getActivityLog(data.activityId);
              obtainedPoints.attempts++;
              
              var askedForHelp = false, // Determines that the player asked for a help after last attempt to solve this activity
                failedHelpTimeout = false, // Determines that the player failed the help timeout
                giveBonusTo = []; // Determines how many players must receive a collaboration bonus if the answer is correct and the timeout was respected
              
              for(var helpType in obtainedPoints.helps) {
                var usedHelp = obtainedPoints.helps[helpType];
                // If help was used after last attempt to answer
                if(typeof usedHelp === 'object') {
                  // If is help with timeout
                  if(helpType in data.helps) {
                    // If answered before the timeout
                    if(data.helps[helpType]) {
                      if(typeof usedHelp.playerId !== 'undefined') {
                        giveBonusTo.push(usedHelp.playerId);
                      }
                    }
                    
                    // If failed the timeout
                    else failedHelpTimeout = true;
                  }

                  askedForHelp = true;
                  obtainedPoints.helps[helpType] = true;
                }
              }
              
              // Set Scores
              var questionScores = question.scores;

              if(isCorrect) {
                
                // If player failed any help timeout
                if(failedHelpTimeout) {
                  player.setPenalty(session, questionScores, obtainedPoints, send);
                }

                player.setReward(session, game, questionScores, obtainedPoints, send, giveBonusTo);

              }
              else {
                player.setPenalty(session, questionScores, obtainedPoints, send, (askedForHelp? 2 : 1));
              }
              self.updateScores(session, send, player.getSocket(), player.getScores());
              
              // If the answer is correct, the player finished all actvities, and the current scenario has a 'allFinished' jump
              if(isCorrect && send.players[playerId].finished) {
                if (game.isSync()) {
                  session.addJump('allFinished');

                     // Notify Status to other Players
                  var send = { playerId : playerId, status : 'allFinished' };
                  session.forEachPlayer(function(player) {
                    self.updateStatus(send, player.getSocket());
                  }, playerId);
                }
  
                // Switch Scenario
                self.setCurrentScenario('allFinished', session, playerId, scenario);
              }
            }
          });
        }
      }
    }
  },

  getScenarioActivitiesCount : function(scenario) {
    var game = this.getGame();
    if (game) {
      return game.getScenarioActivitiesCount(scenario);
    }
    return null;
  },

  /**
   * Send Players Updated Scores
   *
   * @param Session session - the session object.
   * @param object data - the data to send to the client.
   */
  updateScores : function(session, data, socket, scores) {
    data.team = E.utils.roundScore(this.getScore());
    data.players[socket.id].total = E.utils.roundScore(scores.total);
    
    // Send Scores to All Players
    session.forEachPlayer(function(player) {
      // data = { team: float, players : { playerId : { activityId : string, reward : float, penalty : float, collaboration : float, total : float, activityPoints : float, finished : bool } } }
      player.getSocket().emit('update scores', data);
    });
  },

  /**
   * Get Team Score
   *
   * @return double
   */
  getScore : function() {
    return this.score;
  },

  useHelp : function(data, session, beingHelped, helping, helps, playerId, scenario) {
    if(data && typeof data.activityId === 'string' && typeof data.helpType === 'string') {
      var send = {}, used = false;
      var self = this;

      var this_player = E.games_manager.getPlayer(playerId, "use Help");
      var this_player_id = this_player.getSocket().id;

      // If Player isn't being Helped and isn't Helping
      if(!beingHelped && !helping) {
        
        // If Player has Helps
        if(helps.total > 0) {

          // Get Game and Verify if the specified Activity has the specified Help
          var game = this.getGame(), currentScenario = scenario, help;
          var gameId = game.getID();
          if(game && (help = game.getActivityHelp(currentScenario, data.activityId, data.helpType))) {
           
            // If help wasn't yet used and Player has enough Helps
            var helpsLog = this_player.getActivityLog(data.activityId).helps, maxHelps = game.getScenarioHelps(currentScenario);
            if(!(data.helpType in helpsLog) && (!maxHelps || helps.used < maxHelps)) {
              activity = game.getActivity(currentScenario, data.activityId);
              var activitySource = activity.group? session.getGroupSource(activity.source) : activity.source;

              if (game.isMultiplayer()) {
                var availablePlayers = [];
                   // Get available players to help                 
                  session.forEachPlayer(function(player) {
                    console.log(player.hasSolvedActivity(data.activityId));
                    if(player.hasSolvedActivity(data.activityId) && !player.getHelping() && !player.getBeingHelped()) {
                      availablePlayers.push(player);
                    }
                  }, this_player_id);

                      // If any player available send help data to him
                if(availablePlayers.length > 0) {
                  var player = availablePlayers[E.utils.random(availablePlayers.length - 1)];
                  var toSend = { 
                    helpType: data.helpType,  
                    activityId : data.activityId, 
                    activitySource : activitySource, 
                    playerId : this_player_id,
                    gameId : gameId, 
                    help : help };

                  game.useHelp(toSend, function(request) {
                      self.sendHelpRequest(request, player.getSocket());               
                  }); 

                  this_player.setBeingHelped(player.socket.id, data.activityId, data.helpType);
                  player.setHelping(this_player_id, data.activityId);
                  send.playerId = player.socket.id;
                  used = true;
                  }
                  else {
                    send.warning = 'WH4';
                  }     
              }

              // If is Singleplayer
              else {
                var toSend = { 
                    helpType: data.helpType,  
                    activityId : data.activityId, 
                    activitySource : activitySource, 
                    gameId : gameId, 
                    help : help };

                game.useHelp(toSend, function(response) {
                  this_player.answerHelpRequest(response); 
                });
                
                used = true;
                
              }
             
              // If can use then update helps
              if(used) {
                this_player.updateHelps();
                helpsLog[data.helpType] = { used : true };
                
                if(typeof send.playerId != 'undefined') {
                  helpsLog[data.helpType].playerId = send.playerId;
                }
              }
            }
            else {
              send.warning = 'WH3';
            }
          }
          
        }
        else {
          send.warning = 'WH2';
        }
      }
      else {
        send.warning = 'WH1';
      }
      
      // Send Helps Data
      send.helps = this_player.getTotalHelps();
      this.updateHelps(send, this_player.getSocket());
    }
  },

  updateHelps : function(data, socket) {
    socket.emit('update helps', data);
  },

  /**
   * Send Help Request
   *
   * @param object data - the data to send to the client. 
   */
  sendHelpRequest : function(data, socket) {
    // data = { playerId : string, activityId : string, type: string, options : array(object), select : int };
    socket.emit('help request', data);
  },

  sendHelpData : function(data, socket) {
    socket.emit('help response', data);
  },

  /**
   * Other Player Disconnected
   *
   * @param object data - the data to send to the client.
   */
  otherDisconnected : function(data, socket) {
    socket.emit('other disconnected', data);
  },

  /**
   * On Answer to Help Request
   *
   * @param object data - the data received from the client.
   */
  answerHelpRequest : function(data, session, playerId, scenario) {
    if(data && data.list instanceof Array) {
      var self = this;
      var this_player = E.games_manager.getPlayer(playerId, "Answer Help Request");

        // Get Game
        var game = this.getGame();
        if(game) {

          if(typeof data.activityId === 'string' && typeof data.helpType === 'string' && data.list.length > 0) {
            var other_player = this_player;
          }
          
          // If help data wasn't yet set (for Multiplayer)
          if(game.isMultiplayer()) {
            // If this player is helping other
            if(this_player.getHelping()) {

              // Get the other player and make sure he is still being helped by this player
              other_player = E.games_manager.getPlayer(this_player.getHelping().playerId, "Second Answer Help Request");
              if(other_player && other_player.getBeingHelped() && other_player.getBeingHelped().playerId == this_player.getSocket().id) {
                
                // Set Help Data to send to Other Player
                data.playerId = this_player.getSocket().id;
                data.activityId = this_player.getHelping().activityId;
                data.helpType = other_player.getBeingHelped().helpType;
                
                other_player.beingHelped = false;
                this_player.incHelpsGiven();
              }

              this_player.helping = false;
            }
          }

          // If the activity belongs to the current scenario
          var currentScenario = scenario;
          if(this_player && game.getActivity(currentScenario, data.activityId)) {
            
            // Get Help Timeout
            if(game.isMultiplayer()) {
              var timeout = game.getActivityHelpsTimeout(currentScenario, data.activityId);
              if(timeout) data.timeout = timeout;
            }
            
            var callback = function(results) {
              if(results) 
                data.list = results;
              self.sendHelpData(data, other_player.getSocket());
            };
            
            if(game.isMultiplayer()) {
              var activity = game.getActivity(currentScenario, data.activityId);
              var activitySource = activity.group? session.getGroupSource(activity.source) : activity.source;
              
              // Get Help Data
              switch(data.helpType) {

                case 'hints':
                  E.db.getHints(data.list, activitySource, game.id, callback);
                  break;

                case 'remove':
                  E.db.getIncorrectAnswers(data.list, activitySource, game.id, callback);
                  break;
                  
                default:
                  callback();
                  break;
              }
            }
            else {
              
              // Send Help Data to the Player
              callback();
            }
          }
        }
      
    }
  },

  setReady : function(playerId) {
    var player = E.games_manager.getPlayer(playerId);
    if (player) {
      player.setReady();
    }
  },

  cancelHelpRequest : function(socket) {
    socket.emit('cancel help', true);
  },

  /**
   * Finish Game
   *
   * @param object data - the data to send to the client.
   */
  finishGame : function(data, socket) {
    if(typeof this.sessionId !== 'undefined') {
      delete this.sessionId;
    }
    socket.emit('finish game', data);
    socket.disconnect();
  },

  timedOut : function(playerId, session, scenario) {
    var jumpType = 'timeout';
    var self = this;

    // Get Game and Verify if it has the timeout Jump
    var game = this.getGame();
    if(game && game.hasJump(scenario, jumpType)) {

      //Verificar sincronização
      if (game.isSync())
        game.addJump(jumpType);

      // Notify Status to other Players
      var send = { playerId : playerId, status : jumpType };
      session.forEachPlayer(function(player) {
        self.updateStatus(send, player.getSocket());
      }, playerId);

      // Switch Scenario
      this.setCurrentScenario(jumpType, session, playerId, scenario);
    }
  },

  /**
   * Remove Player
   *
   * @param string playerId - the player id.
   */
  removePlayer : function(playerId, session) {
    var manager = this;

    // Remove Player from Session
    var sessionPlayers = session.getPlayers();
    var index = sessionPlayers.indexOf(playerId);
    if(index != -1) {
      sessionPlayers.splice(index, 1);
    }

    if (!this.removed) {
      var game = this.getGame();
      if(game) {
        // Remove session if it is still waiting and it becomes empty
        if(!this.getScenario() && sessionPlayers.length == 0) {
          if(E.sessions.removeWaiting(session.getLMS(), session.getContext().id, this.gameId)) {
            
            // Remove Session from List and Game
            E.sessions.remove(this.sessionId);
            this.removeSession();
            game.remove();
          }
        }
        
        // Remove session if it has already started and the number of players is below minimum
        else if(this.getScenario() && sessionPlayers.length < game.getPlayers().min) {
          this.finish(false, session);
        }
        
        // Tell other players that this player disconnected
        else if(this.getScenario()) {
          // Remove Disconnected Player Score from Team Score
          var dPlayer = E.games_manager.getPlayer(playerId, "Remove Player");
          if(dPlayer) this.updateScore(-dPlayer.getTotalScore());
          
          // Notify other Players
          var send = { id : playerId, team : manager.getScore() };
          session.forEachPlayer(function(player) {
            manager.otherDisconnected(send, player.getSocket());
          });
          
          // Check Jumps
          for(var jumpType in session.resetFlow()) {
            if(this.setCurrentScenario(jumpType, session, playerId)) break;
          }
        }
      }
    }
  },

    /**
   * Finish Session
   *
   * @param bool log - determines if session must be logged.
   */
  finish : function(log, session) {
    var self = this, send = {};
    var game = this.getGame();
    var thisGameId = this.gameId;

    if(game) {

      //Removed nao pode ser assim tem que tar numa lista para caso haja mais que um jogo?

       if (!this.removed) {
        // Remove Session from List
        E.sessions.remove(session.sessionId);

        // Remove Session from Game
        this.removeSession();

        //Remove Games
        game.remove();

        if(typeof this.sessionId !== 'undefined') {
          delete this.sessionId;
      }

        this.removed = true;
      }

      var lms = session.getLMS();
      var context = session.getContext();

      // If session must be logged and team score greater than zero
      if(log && this.score >= 1) {
        send.thisTeam = { score : Math.round(self.score) };
        send.complete = true;

        // Get best sessions
        E.db.getTopSessionsAndCurrentSessionPosition(send.thisTeam.score, thisGameId, function(topTeams, position) {
          send.thisTeam.position = position;
          send.topTeams = topTeams;

          // Create this session
          var gameScores = game.getScores();
          var scoreToStore = (typeof gameScores.team === 'undefined' || gameScores.team)? send.thisTeam.score : null;
          E.db.createSession(lms, context.id, context.name, scoreToStore, self.gameId, function(sessionId) {

            // Get Bonus and Scores Types List (name : id)
            E.db.getBonusAndScoresTypesList(function(bonusTypes, scoresTypes) {

              session.forEachPlayer(function(player) {
                (function() {
                  var playerBonus = player.logs.bonus;
                  var playerScores = player.scores;
                  var playerActivities = player.logs.activities;

                  // Create Player
                  var gameHelps = game.getHelps();
                  var playerHelps = gameHelps.log? (gameHelps.value - player.getTotalHelps()) : null;
                  var givenHelps = (game.isMultiplayer())? player.getHelpsGiven() : null;
                  E.db.createPlayer(player.name, playerHelps, givenHelps, player.instance, player.userId, sessionId, function(playerId) {
                    send.playerId = playerId;
                    self.finishGame(send, player.getSocket());

                    // Create Players Bonus Logs
                    E.db.createPlayerBonus(playerId, playerBonus, bonusTypes, game.getScenarios());

                    // Create Players Scores Logs
                    E.db.createPlayerScores(playerId, playerScores, scoresTypes, gameScores);

                    // Create Players Activities Logs
                    E.db.createPlayerActivities(playerId, self.gameId, playerActivities, game.getActivities(), session.groups);

                  });

                })();
              });
            });
          });

        });

      }

      // If session must not be logged, means the players left or some error ocurred
      else {
        send.complete = false;
        session.forEachPlayer(function(player) {
          self.finishGame(send, player.getSocket());
        });
      } 
     
    }
  }

};

exports.register = function(socket, data){
  var self = this, send = {};
  var games_manager = {};

  // If Player not yet Registered
  if(!(socket.id in players_list)) {
    
    // Validate Player Name and Avatar
    if(data && typeof data.name === 'string' && data.name.length > 0 && typeof data.avatar === 'string' && data.avatar.length > 0) {
      
      // Validate LMS Data
      if(typeof data.lms !== 'undefined' && typeof data.contextId !== 'undefined' && typeof data.contextName !== 'undefined' && typeof data.instance !== 'undefined' && typeof data.userId !== 'undefined') {
        
        // Validate Game ID
        if(typeof data.gameId !== 'undefined' && data.gameId > 0) {

          var managerId;
          if (!(E.games_manager.checkManager(self.id))) {
            var managerId = "m"+new Date().getTime();
            games_manager = manager_list[managerId] = new self.Games_Manager(data.gameId, managerId);
          }
          else {
            games_manager = manager_list[managerId];
          }

          console.log(managerId);

          E.games.load(data.gameId, data.genreCode, function(game, err) {
            games_list[data.gameId] = game;

            if (game) {
              // Create or Get Existing Session
              var session;
              do {
                session = E.sessions.getWaiting(data.lms, data.contextId, managerId);

              } while(!session.addPlayer(socket.id, game.getPlayers().max));

              session.setManagerId(managerId);
              games_manager.setSessionId(session.id);

              //Set Player Data
              E.players.register(socket, game.getHelps().value, data, session.id, function(player, err) {
                if (player) {
                  players_list[socket.id] = player;

                  // Set Player Scores
                  for(var score in game.getScores()) {
                    if(score != 'team') player.setScores(score, 0);
                  }

                  // Player already Loaded Data
                  if(typeof data.ready !== 'undefined' && data.ready) {
                    player.setReady();
                  }
                  else {
                    // Send to Load List
                    game.getToLoadList(session.getGroups(), function(toLoad) {
                      games_manager.startLoading(toLoad, player.getSocket()); 

                      var activities = toLoad.activities;
                      if(typeof activities.question !== 'undefined' && typeof activities.question[1] !== 'undefined') {
                        session.setGroups(activities.question[1]);
                      }
                    });
                  }
                }
                else send.error = 'EP1'; //TODO: Ver o erro a enviar
              });

              // Set Session 
              session.setContextName(data.contextName);

            } 
            else send.error = 'EG2';
          });
        }
        else send.error = 'EG1';
      }
      else send.error = 'EP2';
    }
    else send.error = 'EP1';
  }

   // Send Errors if Any --------- CHECK SOCKET
  if(typeof send.error !== 'undefined') {
    E.games_manager.getPlayer(socket.id, "Error Load Game").getSocket().socket.emit('unexpected', send);
  }
}

/**
 * Get Game from List
 *
 * @param int id - the game id.
 * @return Game
 */
exports.getGame = function(id) {
  if(id in games_list) {
    return games_list[id];
  }
  console.error('Invalid Game');
  return null;
}

/**

*/
exports.checkGame = function(id) {
  if(id in games_list) {
    return true;
  }
  return false;
}

exports.checkManager = function(id) {
  if(id in manager_list) {
    return true;
  }
  return false;
}

/**
 * Get Game Manager
 *
 * @param int id - the game manager id.
 * @return Game Manager id
 */
exports.getManager = function(id) {
   if(id in manager_list) {
    return manager_list[id];
  }
  console.error('Invalid Manager');
  return null;
}

/**
 * Get Game Manager
 *
 * @param int id - the game manager id.
 * @return Game Manager id
 */
exports.checkSessionManager = function(id) {
   if(id in session_to_manager) {
    return true;
  }
  return false;
}

exports.mapManagerFromPlayer = function(id, callback) {
  var player = E.games_manager.getPlayer(id);
  if (player) {
    var session = player.getSession();
    if (session) {
      var manager = session.getManager();
      if (manager){
        callback(manager);
      }
    }
  }
}

/**
 * Get Player from List
 *
 * @param int id - the player id.
 * @return Player
 */
exports.getPlayer = function(id, error) {
  if(id in players_list) {
    return players_list[id];
  }
  else {
    console.error('Invalid Player');
  return null;
  }

}

/**
 * Remove Game from List
 *
 * @param int id - the game id.
 */
exports.removeGame = function(id) {
  if(id in games_list) {
    delete games_list[id];
  }
}

/**
 * Remove Player from List
 *
 * @param int id - the player id.
 */
exports.unregister = function(id) {
  var player = this.getPlayer(id);
  if(player) {
    player.handleDisconnect();
    delete players_list[id];
  }
}
