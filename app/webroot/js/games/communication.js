/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	/**
	 * Server Communication functions
	 *
	 * @package E.game
	 * @subpackage server
	 * @author Bruno Sampaio
	 */
	$.extend(E.game, {
		
		server : {
			socket : null,
			
			/**
			 * Connect to the Server and Bind Communication Events
			 *
			 * This method established the communication with the games server and 
			 * when it is established it binds the communiction events to wait for some specific types of messages.
			 */
			set : function() {
				var self = this, game = E.game, options = {};
				
				if(this.socket) {
					options['force new connection'] = true;
					game.setReady();
				}
				
				this.socket = io.connect(E.system.sockets, options);
				this.socket.on('connect', function() {

					// Receive Resources and Activities
					this.on('start loading', function(data) {
						self.verify(data);
						game.load(data);
					});

					// Receive Starting Data
					this.on('start game', function(data) {
						self.verify(data);
						game.start(data);
					});
					
					// Receive Next Scenario
					this.on('switch scenario', function(data) {
						self.verify(data);
						game.switchScenario(data);
					});
					
					// Receive Help Request
					this.on('help request', function(data) {
						self.verify(data);
						game.handleHelpRequest(data);
					});
					
					// Receive Help Response
					this.on('help response', function(data) {
						self.verify(data);
						game.handleHelpResponse(data);
					});
					
					// Cancel Help Request
					this.on('cancel help', function() {
						game.stopWaitingToHelp();
					});
					
					// Update Scores
					this.on('update scores', function(data) {
						self.verify(data);
						game.updateScores(data);
					});
					
					// Update Helps
					this.on('update helps', function(data) {
						self.verify(data);
						game.updateHelps(data);
					});
					
					// Update Status
					this.on('update status', function(data) {
						self.verify(data);
						game.updateStatus(data);
					});
					
					// Receive Ending Data
					this.on('finish game', function(data) {
						self.verify(data);
						game.finish(data);
					});
					
					// Receive Unexpected Problems
					this.on('unexpected', function(data) {
						self.verify(data);
					});
					
					// Other Player was Disconnected
					this.on('other disconnected', function(data) {
						self.verify(data);
						if(data && typeof data.id !== 'undefined' && typeof data.team !== 'undefined') {
							game.players.removePlayer(data.id);
							game.scores.setValue(data.team, 'team');
						}
					});

					// Player was Disconnected
					this.on('disconnect', function() {
						if(!game.finished) {
							game.finish({ complete : false });
							game.modal.setError('ELC');
						}
					});

				});
			},
			
			
			/**
			 * Verifies if there are no errors or warnings on the received message from the server.
			 * This method is executed each time a message is received.
			 *
			 * @param object data - the data received from the server.
			 */
			verify : function(data) {
				
				// Set Error
				if(typeof data.error !== 'undefined') {
					E.game.modal.setError(data.error);
				}
				
				// Set Warning
				if(typeof data.warning !== 'undefined') {
					E.game.modal.setWarning(data.warning);
				}
				
			},
			
			
			/**
			 * Sends data to the server.
			 * 
			 * If a connection with the server is established this method will send a message to it.
			 * This message is identified by an 'action' and contains an object with several arguments for the server to interpret.
			 *
			 * @param string action - the action name.
			 * @param object args - the message arguments.
			 */
			send : function(action, args) {
				if(this.socket) {
					this.socket.emit(action, args);
				}
			}
			
		}
		
	});
	
});