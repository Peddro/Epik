E = {};

// Get Server Configuration
E.config = require('./config.json');

// Load Socket.io and Files Modules
E.io = require('socket.io').listen(E.config.port, { log : false });
E.files = require('fs');

// Load Modules
E.db = require('./database')
E.games = require('./games');
E.games_manager = require('./games_manager');
E.sessions = require('./sessions');
E.players = require('./players');
E.utils = require('./utils');

E.quiz = require('./Quiz/quiz');
E.cquiz = require('./Quiz/cquiz');
E.iquiz = require('./Quiz/iquiz');

// Start Database Connection
E.db.connect();

// Configure Transport Types
E.io.configure(function () {
  this.set('transports', ['websocket', 'htmlfile', 'xhr-polling', 'jsonp-polling']);
});

// Start Server
E.io.sockets.on('connection', function(socket) {
  var self = E.io, games_manager = E.games_manager;

  socket.on('set player', function(data) { 
    games_manager.register(socket, data);
  });

  socket.on('set ready', function() {
    self.getManager(socket, function(manager) {
      manager.setReady(socket.id);
    });
  });

  socket.on('click button', function(data) {
    self.getManager(socket, function(manager) {
      manager.clickButton(data);
    });
  });

  socket.on('answer question', function(data) {
    self.getManager(socket, function(manager) {
      manager.answerQuestion(data);
    });
  });
  
  socket.on('request help', function(data) {
    self.getManager(socket, function(manager) {
      manager.useHelp(data);
    });
  });

  socket.on('answer help request', function(data) {
    self.getManager(socket, function(manager) {
      manager.answerHelpRequest(data);
    });
  });

  socket.on('timed out', function() {
    self.getManager(socket, function(manager) {
      manager.timedOut();
    });
  });
  
  socket.on('disconnect', function () {
    games_manager.unregister(socket.id);
  });
  
});

/**
 * Get Manager
 *
 * @param object socket - the client socket.
 * @param function callback - the callback function.
 */
E.io.getManager = function(socket, callback) {
  E.games_manager.mapManagerFromPlayer(socket.id, function(manager) {
    callback(manager);
  });
}