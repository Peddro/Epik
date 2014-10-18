/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	/**
	 * Quiz Game functions
	 *
	 * @package E.game
	 * @author Bruno Sampaio
	 */
	$.extend(E.game, {
		
		penaltiesToWarn : 3, // Number of penalties allowed before warning the player about helps
		
		clicked : false,
		waiting : {
			toJump: false, // Waiting for a jump
			toHelp: false, // Waiting for player to send help
			forHelp: false // Waiting for player to receive help
		},
		finished : false,
		
		
		/**
		 * Starts the Game
		 *
		 * This method must receive the first scenario id and the players data.
		 * 
		 * @param object data - the data received from the server.
		 */
		start : function(data) {
			if(data && typeof data.scenarioId === 'string' && data.players instanceof Array) {
				
				// Set Players
				this.players.setInfo(data.players);
				this.players.createDOM();
				this.players.setPlayersNumber(data.players.length);

				// Set Starting Scenario
				data.name = E.defaults.screens.game;
				this.switchScenario(data);
				
				// Play Background Music
				this.sounds.playBackground();
			}
		},
		
		
		/**
		 * Finishes the Game
		 *
		 * This method must receive at least a value determining if the game was finished normally.
		 * It can also receive the current player ID, the top teams scores and this team score.
		 * In the end the connection with the server is closed.
		 * 
		 * @param object data - the data received from the server.
		 */
		finish : function(data) {
			if(data && typeof data.complete === 'boolean') {
				var screenName = (data.complete)? E.defaults.screens.rankings : E.defaults.screens.gameover;
				this.finished = true;
				
				// Send Session Id
				if(typeof data.playerId === 'number') {
					$.post(E.system.server + 'players/send/' + data.playerId);
				}
				
				// Create Scores HTML
				if(data.topTeams instanceof Array && typeof data.thisTeam === 'object') {
					var dom = this.getScreen().children('.scenario.'+screenName).children('.text').children('div'), 
						position = 1,
						found = false;
					
					// Set Top 3 Teams Scores
					for(var i = 0; i < data.topTeams.length; i++) {
						
						if(!found && data.thisTeam.position == position) {
							$(dom.get(position - 1)).addClass('current').children('.value').text(data.thisTeam.score);
							found = true;
							position++;
						}
						
						if(position <= 3) {
							$(dom.get(position - 1)).children('.value').text(data.topTeams[i].score);
							position++;
						}
					}
					
					// Set Current Team Score
					var lastDOM = $(dom.get((position <= 3)? position-1 : dom.length - 1));
					if(found) {
						dom.filter('.ellipsis').hide();
						lastDOM.hide();
					}
					else {
						lastDOM.addClass('current');
						lastDOM.children('.position').text(data.thisTeam.position);
						lastDOM.children('.value').text(data.thisTeam.score);
					}
					lastDOM.nextAll().hide();
				}

				// Set Ending Scenario
				this.switchScenario({ name : screenName });

				// Stop Background Music
				this.sounds.stopBackground();
			}
		},
		
		
		/**
		 * Set HTML Scenarios Events
		 *
		 * Set the events for all buttons on the start, instructions, waiting room, game over and rankings screens.
		 */
		setScenariosEvents : function() {
			var self = this, screens = E.defaults.screens, container = this.getScreen(), current;

			// Set Start Scenario Events
			current = container.children('.'+screens.start+'.scenario');
			current.find('.button.play').click(function(event) {			
				var waitingScreen = container.children('.'+screens.wait);
				
				// Enable and Focus Name Input
				waitingScreen.find('input[name=player-name]').attr('disbaled', false).focus();
				
				// Show Buttons
				waitingScreen.children('.buttons').show();
				
				self.setScenario(null, screens.wait);
			});

			current.find('.button.help').click(function(event) {
				self.setScenario(null, screens.instructions);
			});
			
			
			// Set Wait Scenario Events
			current = container.children('.'+screens.wait+'.scenario');
			
			// Set Player Name
			var playerName = E.tmp.player.name;
			if(playerName) {
				current.find('input[name=player-name]').attr({ value : playerName, disabled : true });
			}
			
			current.find('.button.start').click(function() {
				$(this).parents('.scenario').find('form').submit();
			});
			
			// Set Form Submit Event
			current.find('form').submit(function(event) {
				var player = E.tmp.player;
				event.preventDefault();
				
				// Get Player Nickname and Avatar
				var playerNameInput = $(this).children('input[name=player-name]');
				var playerName = playerNameInput.attr('value');
				var chooser = $(this).children('.choose');
				var playerAvatar = chooser.find('.'+classes.selected);
				
				// Check if a nickname and avatar are provided
				if(playerName.length > 0 && playerAvatar.length > 0) {
					
					// Disable name input and hide buttons
					playerNameInput.attr(classes.disabled, true);
					$(this).siblings('.buttons').fadeOut('fast');
					
					// Set player data
					player.name = playerName;
					player.avatar = playerAvatar.attr('src');
					
					// Open connection with the server and send player data
					self.server.set();
					self.server.send('set player', E.tmp.player);

					delete E.tmp.player;
				}
				else {
					self.modal.setError('EP1');
				}
			});
			
			
			// Set Chooser Events
			current = container.find('.choose');
			current.each(function(index, chooser) {
				var chooserList = $(chooser).children('.current').children('.list');
				
				// Click Left Arrow
				$(chooser).children('.arrow-left').click(function() {
					chooserList.fadeOut('fast', function() {
						var selectedItem = chooserList.children(':first').detach();
						selectedItem.removeClass(classes.selected);
						chooserList.append(selectedItem);
						
						// Set Currently Selected Item
						chooserList.children(':first').addClass(classes.selected);
						chooserList.fadeIn('fast');
					});
				});

				// Click Right Arrow
				$(chooser).children('.arrow-right').click(function() {
					chooserList.fadeOut('fast', function() {
						chooserList.children(':first').removeClass(classes.selected);
						
						// Set Currently Selected Item
						var selectedItem = chooserList.children(':last').detach();
						selectedItem.addClass(classes.selected);
						chooserList.prepend(selectedItem);
						chooserList.fadeIn('fast');
					});
				});
			});
			
			
			// Set General Buttons Events
			var buttonsSelector = '.scenario > .buttons >';
			
			container.find(buttonsSelector+' .button.back').click(function() {
				self.setScenario(null, screens.start);
			});
			
			container.find(buttonsSelector+' .button.repeat').click(function() {
				parent.location.reload();
			});
			
			container.find(buttonsSelector+' .button.exit').click(function() { 
				window.location = E.system.server; 
			});
		},
		
		
		/**
		 * Set the Player as Ready to Play
		 *
		 * This method tells the server the player is ready to start playing.
		 * 
		 * @param object send - the data to sent to the server.
		 */
		setReady : function(send) {
			if(this.players.getPlayersNumber() > 1) {
				this.modal.setWaiting('start');
			}
			
			if(send) {
				this.server.send('set ready', true);
			}
		},
		
		
		/**
		 * Switch Scenario
		 *
		 * This method must receive at least a scenario ID or a scenario class name.
		 * It resets all game objects current state and switches to the specified scenario.
		 * 
		 * @param object data - the data received from the server.
		 */
		switchScenario : function(data) {
			if(data && (typeof data.scenarioId === 'string' || typeof data.name === 'string')) {
				
				// Reset Timers and Close Modal Window
				this.timeManager.clearAll();
				this.modal.close();
				
				// Reset Players Status, Clicked, Waiting and Cursor
				if(typeof this.players !== 'undefined') {
					this.players.addStatus();
				}
				this.clicked = false;
				this.waiting.toJump = false;
				this.waiting.toHelp = false;
				this.waiting.forHelp = false;
				this.utils.setCursor();
				
				// Hide TipTips
				$('#tiptip_holder').hide();
				
				// Set Current Game Scenario
				this.setScenario(data.scenarioId, data.name);
				
				// Set Scenario Helps
				this.scores.setLimit(data.helps, 'helps');
				
				// Set Scenario Timeout
				if(typeof data.timeout == 'number') {
					var self = this;
					this.timer.set(this.timer.types[0], data.timeout, function() {
						self.setWaitingToJump('timeout');
						self.server.send('timed out', true);
					});
				}
				
				
			}
		},
		
		
		/**
		 * Determines if the Player is waiting to Jump
		 * @return bool
		 */
		isWaitingToJump : function() {
			return this.waiting.toJump;
		},
		
		
		/**
		 * Determines if the Player is Helping other Player
		 * @return bool
		 */
		isWaitingToHelp : function() {
			return this.waiting.toHelp;
		},
		
		
		/**
		 * Determines if the Player is waiting for Help
		 * @return bool
		 */
		isWaitingForHelp : function() {
			return this.waiting.forHelp;
		},
		
		
		/**
		 * Sets the Player Waiting Status to Waiting for Jump
		 *
		 * @param string type - the jump type.
		 */
		setWaitingToJump : function(type) {
			if(this.players.getPlayersNumber() > 1) {
				this.modal.setWaiting('jump'+type.ucfirst());
			}
			
			this.players.addStatus(this.players.getCurrentPlayer().id, type);
			this.waiting.toJump = true;
			this.waiting.forHelp = false;
			
			// Disable Activities
			$('.activity').find('input, helps > img').attr('disabled', true).disable();
		},
		
		
		/**
		 * Update Scores
		 *
		 * This method must receive a players array and for each player can contain
		 * a reward score, a penalty score, a collaborations score, and a total score.
		 * In addition to that it can also contain a variable determining if he finished the scenario.
		 * Besides players data it can also contain the team score.
		 * 
		 * @param object data - the data received from the server.
		 */
		updateScores : function(data) {
			if(data && typeof data.players !== 'undefined') {
				var players = E.game.players, 
					scores = E.game.scores, 
					sounds = E.game.sounds, 
					currentPlayer = players.getCurrentPlayer();
					
				// Set Team Score
				if(typeof data.team === 'number') {
					players.setScoreValue(data.team, 'team');
				}
				
				// Set Players Scores	
				for(var playerId in data.players) {
					var playerData = data.players[playerId], isCurrent = currentPlayer.id == playerId;
					
					if(typeof playerData.activityId === 'string' && (playerData.activityId in this.activities)) {
						var activity = this.activities[playerData.activityId];
						
						// Set Player Reward Score
						var hasReward = typeof playerData.reward === 'number';
						if(hasReward) {
							players.setScoreValue(playerData.reward, 'reward', playerId);
							if(isCurrent) {
								activity.setCorrect(playerData.solution);
								activity.setSolved();
								sounds.playCorrect();
								
								// Finish collaboration if there was any
								var waiting = this.isWaitingForHelp();
								if(waiting && waiting.activityId == playerData.activityId) {
									this.stopWaitingForHelp();
								}
							}
							else if(!activity.isSolved() && !this.isWaitingToJump()) {
								activity.enableHelps();
							}
						}
						
						// Set Player Penalty Score
						if(typeof playerData.penalty === 'number') {
							players.setScoreValue(playerData.penalty, 'penalty', playerId);
							if(isCurrent && !hasReward) {
								
								// Check if player must be warned about helps
								if(this.penaltiesToWarn === 0) {
									this.modal.setWarning('WH6');
									delete this.penaltiesToWarn;
								}
								else if(typeof this.penaltiesToWarn !== 'undefined') {
									this.penaltiesToWarn--;
								}
								
								activity.setIncorrect(playerData.solution);
								sounds.playIncorrect();
							}
						}
						
						// Update Activity Reward Score
						if(isCurrent && typeof playerData.activityPoints === 'number') {
							activity.setScore(playerData.activityPoints, 'reward');
						}
					}
					
					// Set Player Collaboration Score
					if(typeof playerData.collaboration === 'number') {
						players.setScoreValue(playerData.collaboration, 'collaboration', playerId);
					}
					
					// Set Player Total Score
					if(typeof playerData.total === 'number') {
						players.setScoreValue(playerData.total, 'total', playerId);
					}
					
					// Player finished all activities and still is on the same scenario
					if(isCurrent && typeof playerData.finished === 'boolean' && playerData.finished) {
						this.setWaitingToJump('allFinished');
					}
				}
			}
		},
		
		
		/**
		 * Update Player Helps
		 *
		 * This method must receive at least the player number of helps.
		 * Besides that it can also receive the player helping the current player.
		 * 
		 * @param object data - the data received from the server.
		 */
		updateHelps : function(data) {
			if(data && typeof data.helps === 'number') {
				
				// Set Player Helps
				E.game.scores.setValue(data.helps, 'helps');
				
				var waiting = false;
				if(typeof data.playerId === 'string' && (waiting = this.isWaitingForHelp())) {
					var players = E.game.players;
					players.setCollaborating(data.playerId, 'helping');
					waiting.playerId = data.playerId;
				}
			}
		},
		
		
		/**
		 * Update Player Status
		 *
		 * This method must receive a player ID and the player status code.
		 * 
		 * @param object data - the data received from the server.
		 */
		updateStatus : function(data) {
			if(data && typeof data.playerId === 'string' && typeof data.status === 'string') {
				E.game.players.addStatus(data.playerId, data.status);
			}
		},
		
		
		/**
		 * Handle Help Request
		 *
		 * This method must receive a player ID, an activity ID, a help type, 
		 * a list of options, and the number of options to select.
		 * 
		 * @param object data - the data received from the server.
		 */
		handleHelpRequest : function(data) {
			if(data && typeof data.playerId === 'string' && typeof data.activityId === 'string' && typeof data.helpType === 'string' && data.options instanceof Array && typeof data.select === 'number') {
				
				var activity = this.activities[data.activityId];
				if(typeof activity !== 'undefined') {
					var players = this.players, status = 'help', extra;
					
					// Set status (Waiting to help must be set first)
					this.waiting.toHelp = { playerId : data.playerId };
					players.setCollaborating(data.playerId, status);
					
					// If is Question
					if(activity instanceof this.Question) {
						extra = activity.question;
					}

					// Set Selector
					var modal = this.modal.setSelector(status + data.helpType.ucfirst() + 'Request', extra, data.options, data.select);

					// Set Play Help Sound Interval
					this.timeManager.setInterval(status, function() {
						E.game.sounds.playHelp();
					}, 4000);
					
					var self = this, selected = 0;
					modal.find('.select > .list > .item').click(function(event) {
						
						// Deselect Item
						if($(this).hasClass(classes.selected)) {
							$(this).removeClass(classes.selected);
							selected--;
						}
						else {
							
							// Select Item
							if(selected < data.select) {
								$(this).addClass(classes.selected);
								selected++;
							}
							
							// If all needed items selected
							if(selected == data.select) {
								var selectedDOM = $(this).parent().find('.item.selected'),
									list = [];
								
								// Create list of items to send to other player
								selectedDOM.each(function(index, item) {
									var itemId = $(item).attr('id');
									list.push(parseInt(itemId.substr(1, itemId.length)));
								});
								
								// Send the list to the server
								self.server.send('answer help request', { list : list });
								
								// Remove player status and close modal window
								self.stopWaitingToHelp(data.playerId, modal);
							}
						}
					});
				}
			}
		},
		
		
		/**
		 * Stop Waiting to Help
		 *
		 * If player is helping other, this method will reset the waiting to help flag.
		 * 
		 * @param string playerId - the player being helped ID.
		 * @param DOMElement modal - the modal window DOM.
		 */
		stopWaitingToHelp : function(playerId, modal) {
			if(this.isWaitingToHelp()) {
				if(!playerId) playerId = this.waiting.toHelp.playerId;
				if(!modal) modal = this.modal.get(this.modal.types[0]);

				// Remove player status and the interval to play the help sound
				this.players.setNormal(playerId);
				this.timeManager.clearInterval('help');

				// Close the modal window
				this.modal.close(modal);

				// Reset waiting status
				this.waiting.toHelp = false;
			}
		},
		
		
		/**
		 * Handle Help Response
		 *
		 * This method must receive an activity ID, a help type, and a list of values.
		 * 
		 * @param object data - the data received from the server.
		 */
		handleHelpResponse : function(data) {
			if(data && typeof data.activityId === 'string' && typeof data.helpType === 'string' && (data.list instanceof Array) && data.list.length > 0) {
				
				if(this.isWaitingForHelp()) {
					
					var activity = this.activities[data.activityId], hasTimeout = typeof data.timeout === 'number';
					if(typeof activity !== 'undefined' && !activity.isSolved()) {
						var extra;

						// If is Question
						if(activity instanceof this.Question) {
							extra = activity.question;

							if(data.helpType == 'remove') {
								for(var i = 0; i < data.list.length; i++) {
									activity.setIncorrect(data.list[i].id);
								}
							}
						}
						
						if(data.helpType == 'resource') {
							activity.useResourceHelp(data.list, data.helpType);
						}
						else {
							
							// Set Help Modal Window
							var modal = this.modal.setHelp('help' + data.helpType.ucfirst() + 'Response', extra, data.list, hasTimeout);
							activity.setHelpData(modal.find('.help')[0], data.helpType);

							// Set Timer
							if(hasTimeout) {
								var timeoutId = this.timer.types[1] + activity.id + data.helpType;
								activity.setHelpTimeout(timeoutId, data.helpType);
								this.timer.set(timeoutId, data.timeout, function() {
									activity.setHelpTimeout(false, data.helpType);
								});
							}
						}

						// Play Help Sound
						this.sounds.playHelp();
					}
					
					this.stopWaitingForHelp(data.playerId);
				}
			}
		},
		
		
		/**
		 * Stop Waiting for Help
		 *
		 * If player is waiting for help resets the other player status 
		 * and the waiting for help flag.
		 * 
		 * @param string playerId - the player helping the current player.
		 */
		stopWaitingForHelp : function(playerId) {
			if(this.isWaitingForHelp()) {
				if(!playerId) playerId = this.waiting.forHelp.playerId;

				// Remove other player Status
				if(typeof playerId === 'string') {
					this.players.setNormal(playerId);
				}

				// Close waiting window
				this.modal.close(this.modal.get(this.modal.types[1]));
				
				// Reset waiting status
				this.waiting.forHelp = false;
			}
		},
		
		setSync : function(value) {
			this.sync = value;
		},
		
		/**
		 * Load Game Data
		 *
		 * This method must receive the game resources and activities data.
		 * It loads the game data contained on E.tmp.data.
		 * 
		 * @param object data - the data received from the server.
		 */
		load : function(data) {
			if(data && typeof data.resources !== 'undefined' && typeof data.activities !== 'undefined') {
				$.extend(E.defaults.resources, data.resources);
				E.defaults.activities = data.activities;

				var self = this, data = E.tmp.data, resources = E.defaults.resources, activities = E.defaults.activities;

				var loadProperties = function(list, progress) {
					var properties = E.defaults.properties;

					// Set Scores
					var scores = self.scores = new self.Scores(), scoresList = list[properties[1]];
					for(var key in scoresList) {
						var score = scoresList[key];

						if(key == 'team') {
							scores.setTeamScore(score.name, score.log);
						}
						else {
							scores.addPlayerScore(key, score.name, score.log);
						}
					}

					// Set Helps
					var helps = list[properties[2]];
					scores.setHelps(helps.name, helps.value, helps.log);

					// Set Sync
					var sync = list[properties[5]];
					self.setSync(sync.value);

					// Set Players
					var players = list[properties[3]];
					self.players = new self.Players(null, players.styles);
					self.players.setPlayersNumber(players.max);

					// Set Sounds
					var sounds = self.sounds = new self.Sounds(), soundsList = list[properties[4]];
					for(var key in soundsList) {
						var soundId = soundsList[key];
						sounds.setSourceId(soundId, key);
						if(soundId) {
							sounds.setFile(resources[soundId].object, key);
						}
					}

					// Increment Progress Bar
					progress.increment();
				};

				var loadScenarios = function(list, progress) {
					self.scenarios = {};

					for(var id in list) {
						var scenario = list[id], contents = scenario.contents;

						// Create Scenario
						self.scenarios[id] = new self.Scenario(id, null, scenario.type, contents, null, null, scenario.styles);

						// Increment Progress Bar
						progress.increment();
					}
				};

				var loadCollection = function(list, collection, progress) {
					var icons = E.defaults.icons;

					for(var tag in list) {
						var elements = list[tag];
						for(var i = 0; i < elements.length; i++) {
							var item = elements[i], id = item.id, source = item.source, name = null, content = item.content, styles = item.styles;

							switch(tag) {
								case icons.heading:
									collection[id] = new self.Heading(id, name, content, styles);
									break;

								case icons.paragraph:
									collection[id] = new self.Paragraph(id, name, content, styles);
									break;

								case icons.button:
									collection[id] = new self.Button(id, name, item.type, content, styles);
									break;

								case icons.line:
									collection[id] = new self.Line(id, name, styles);
									break;

								case icons.square:
									collection[id] = new self.Square(id, name, content, styles);
									break;

								case icons.circle:
									collection[id] = new self.Circle(id, name, content, styles);
									break;

								case icons.balloon:
									collection[id] = new self.Balloon(id, name, content, styles);
									break;

								case icons.audio:
									collection[id] = new self.Audio(id, name, source, styles);
									collection[id].setDOM(resources[source]);
									break;

								case icons.image:
									collection[id] = new self.Image(id, name, source, styles);
									collection[id].setData(resources[source]);
									break;

								case icons.video:
									collection[id] = new self.Video(id, name, source, styles);
									collection[id].setDOM(resources[source]);
									break;

								case icons.pdf:
									collection[id] = new self.PDF(id, name, source, styles);
									collection[id].setDOM(resources[source]);
									break;

								case icons.question:
									source = item.group? activities[tag][1][source] : source;
									collection[id] = new self.Question(id, name, source, item.group, item.scores, item.helps, styles);
									collection[id].setData(activities[tag][0][source]);
									break;
							}

							// Increment Progress Bar
							progress.increment();
						}
					}
				};

				this.utils.loadResources(E.strings.loading.resources, resources, function() {
					var collections = E.defaults.collections;

					// Count Elements to Load
					var sum = 1;
					for(var i = 1; i < collections.length; i++) {
						var collection = collections[i];

						// Set number of elements to load
						if(collection in data) {
							for(var element in data[collection]) {
								sum+= data[collection][element].length;
							}
						}
					}
					sum+= Object.keys(data[collections[0]]).length;

					var progress = new self.Loading(E.strings.loading.game, sum);

					// Load Properties
					loadProperties(data.properties, progress);

					// Load Collections
					for(var i = 1; i < collections.length; i++) {
						var collection = collections[i];
						if(collection in data) {
							loadCollection(data[collection], self[collection], progress);
						}
					}

					// Load Scenarios
					loadScenarios(data[collections[0]], progress);

					if(progress.isFinished()) {
						self.setReady(true);
						delete E.tmp.data;
					}

				});
			}
		}
		
	});
	
	
	/**
	 * Buttons Events
	 */
	E.game.Button.prototype.events = function() {
		var self = this, game = E.game;
		
		var hover = game.utils.highlight(self.styles.background.color);
		
		this.shape.on('mouseover', function() {
			if(!game.waiting.toJump) {
				this.get('.shape')[0].setFill(hover);
				game.utils.setCursor('pointer');
				this.getLayer().draw();
			}
		});

		this.shape.on('mouseout', function() {
			if(!game.waiting.toJump) {
				this.get('.shape')[0].setFill(self.styles.background.color);
				game.utils.setCursor();
				this.getLayer().draw();
			}
		});
		
		this.shape.on('click', function(event) {
			
			// If the button clicked isn't the same as last one
			if(event.which == 1 && !game.waiting.toJump) {
				
				// If there are more players set a waiting window
				game.setWaitingToJump(self.type);
				
				game.server.send('click button', { buttonId : self.id, buttonType : self.type });
				game.clicked = self.id;
			}
		});
	};
	
	
	// Extend Audio, Video and PDF Resources
	var rExtension = {
		
		createShape : function() {
			if(this.image && this.animations) {

				this.shape = new Kinetic.Sprite({
					id: this.id,
					image: this.image,
					animation: 'move', 
					animations: this.animations, 
					width: this.styles.width,
					height: this.styles.height,
					stroke: this.styles.border.color,
					strokeWidth: this.styles.border.thickness,
					rotationDeg: this.styles.rotation,
					frameRate: 5,
					dragBoundFunc: E.game.utils.dragBounds
				});
				
				this.events();
			}
		},
		
		animate : function() {
			if(this.shape !== 'undefined' && this.shape instanceof Kinetic.Sprite) {
				this.shape.start();
			}
		},
		
		events : function() {
			var game = E.game, dom = this.dom;
			
			game.utils.setSoundEvents(dom);
			
			this.shape.on('mouseover', function() {
				game.utils.setCursor('pointer');
			});

			this.shape.on('mouseout', function() {
				game.utils.setCursor();
			});

			this.shape.on('click', function(event) {
				
				// If game isn't waiting for anything from the server
				if(event.which == 1 && !game.isWaitingToHelp()) {
					game.modal.setGeneral(dom);
				}
			});
		},
		
		setData : function(value) {
			var resource = E.defaults.resources[value];
			if(typeof resource !== 'undefined' && resource.object instanceof Image) {
				this.image = resource.object;
				this.animations = { move : [] };

				var size = this.image.height;
				for(var i = 0; i < this.image.width; i+= size) {
					this.animations.move.push({ x : i, y : 0, width: size, height: size });
				}
			}
		}
		
	};
	$.extend(E.game.Audio.prototype, rExtension);
	$.extend(E.game.Video.prototype, rExtension);
	$.extend(E.game.PDF.prototype, rExtension);
	
	
	// Extend Questions
	$.extend(E.game.Question.prototype, {
		
		events : function() {
			var self = this, 
				game = E.game,
				modal = game.modal,
				questionsTypes = E.defaults.types.question,
				helpsDOM = this.dom.find('.helps');

			// Send Answer
			var sendAnswer = function(answerDOM) {

				// If game isn't waiting for anything from the server
				if(!game.isWaitingToJump()) {
					
					var solution = answerDOM.val(), type = answerDOM.attr('type');
					if((type == 'text' && solution.length > 0) || (type == 'radio' && (solution = parseInt(solution)) > 0)) {
						var send = { activityId : self.id, answer : solution, helps : {} };
						
						// Check for Helps Timeouts
						for(var helpType in self.helps) {
							var help = self.helps[helpType];
							
							// If is still counting, reset the timer and add this help to the data to send
							if(typeof help.timeout !== 'undefined') {
								if(help.timeout) {
									send.helps[helpType] = true;

									game.timeManager.clearInterval(help.timeout);
									game.timeManager.clearTimeout(help.timeout);
									game.timer.hide($(help.data));

									help.timeout = false;
								}
								else send.helps[helpType] = false;
							}
						}
						
						// Send Answer
						game.server.send('answer question', send);
						self.dom.addClass(classes.ajax);
					}
				}
			};

			// Answer Events
			if(this.type == questionsTypes[1]) {
				var input = this.dom.find('input[type=text]'), timeoutId = this.id + 'Answer';
				
				// On keyup send the answer
				input.keyup(function(){
				    game.timeManager.setTimeout(timeoutId, function() {
				    	sendAnswer(input);
				    }, 500);
				});

				// On keydown, clear the timeout
				input.keydown(function(){
				    game.timeManager.clearTimeout(timeoutId);
				});
			}
			else if(this.type == questionsTypes[2] || this.type == questionsTypes[3]) {
				this.dom.find('input[type=radio]').bind('change', function() {
					sendAnswer($(this));
				});
			}


			// Request Help
			var requestHelp = function(dom, type) {
				var dom = dom.find('.'+type);
				
				// Disable Hints and Remove Helps
				if(E.defaults.mode == 2 && (type == 'hints' || type == 'remove')) {
					dom.disable();
				}
				
				// Help Event
				dom.click(function(event) {

					// If game isn't waiting for help or to jump
					if(event.which == 1 && !game.isWaitingToJump()) {
						
						if(!game.isWaitingForHelp()) {
							if(!game.isWaitingToHelp()) {

								// If help isn't disabled
								if(!$(this).isDisabled()) {

									var help = self.helps[type];

									// If help not yet used
									if(typeof help.data === 'undefined' || !help.data) {

										// If player still has helps
										var totalHelps = game.scores.getHelps(true);
										if(totalHelps > 0) {
											var toSend = { activityId : self.id, helpType : type };
											
											switch(type) {

												// Resource Help
												case 'resource':
													if(help.selected && !help.data) {
														self.useResourceHelp(help.selected, type);
													}
													break;

												// Hints Help
												case 'hints':
													self.setHelpData(null, type);
													break;
													
												// Remove Help
												case 'remove':
													toSend.selected = [];
													self.dom.children('.answers').find('input['+classes.disabled+']').each(function(index, value) {
														toSend.selected.push($(value).val());
													});
													self.setHelpData(null, type);
													break;
											}

											// Set Waiting for Help
											if(!help.data) {
												modal.setWaiting('help'+type.ucfirst());
												game.waiting.forHelp = { activityId : self.id };
											}

											game.server.send('request help', toSend);
											game.scores.setValue(game.scores.getHelps() - 1, 'helps');
										}
										else {
											modal.setWarning('WH3');
											game.sounds.playIncorrect();
										}
									}
									else {
										modal.setGeneral(help.data);
									}
									
									// Remove helps tip
									if(typeof game.penaltiesToWarn !== 'undefined') {
										delete game.penaltiesToWarn;
									}
								}
							}
							else {
								modal.setWarning('WH1');
								game.sounds.playIncorrect();
							}
						}
						else {
							modal.setWarning('WH5');
							game.sounds.playIncorrect();
						}
					}
				});
			};

			// Set Helps Events
			for(var helpType in this.helps) {
				requestHelp(helpsDOM, helpType);
			}

		},
		
		useResourceHelp : function(list, type) {
			var self = this, resources = E.defaults.resources, utils = E.game.utils;
			
			var callback = function(data) {
				self.setHelpData(data, type);
				utils.setSoundEvents(data);
				E.game.modal.setGeneral(data);
			}
			
			$.each(list, function(index, value) {
				if(index in resources) {
					callback(resources[index].object);
				}
				else if(value.id in resources) {
					callback(resources[value.id].object);
				}
				else {
					var toLoad = {};
					toLoad[value.id] = value;
					utils.loadResources(E.strings.loading.resources, toLoad, function() {
						callback(toLoad[value.id].object);
					});
				}
			});
		},
		
		setHelpData : function(value, type) {
			if(typeof this.helps[type] !== 'undefined') {
				this.helps[type].data = value;
			}
		},
		
		setHelpTimeout : function(value, type) {
			if(typeof this.helps[type] != 'undefined') {
				this.helps[type].timeout = value;
			}
		},
		
		enableHelps : function() {
			var helpsDOM = this.dom.find('.helps');
			for(var type in this.helps) {
				
				// If help wasn't yet used
				if(typeof this.helps[type].data == 'undefined') {
					helpsDOM.find('img.'+type).enable();
				}
			}
		},
		
		disableHelps : function(type) {
			var selector = '.helps img' + ((type)? '.'+type : '');
			this.dom.find(selector).disable();
		},
		
		setCorrect : function(id) {
			this.dom.removeClass(classes.ajax);
			
			var input, types = E.defaults.types.question;
			if(this.type == types[1]) {
				input = this.getAnswerInput().removeClass('incorrect');
			}
			if(this.type == types[2] || this.type == types[3]) {
				input = this.getAnswerInput(id).parent().parent();
			}
			input.addClass('correct');
		},
		
		setIncorrect : function(id) {
			this.dom.removeClass(classes.ajax);
			
			// Disable answer input and label
			var input, types = E.defaults.types.question;
			if(this.type == types[1]) {
				input = this.getAnswerInput();
			}
			else if(this.type == types[2] || this.type == types[3]) {
				input = this.disableAnswer(id).parent().parent();
			}
			input.addClass('incorrect');
			
			// Disable remove help if there are only two answers left
			var helpType = 'remove';
			if(helpType in this.helps) {
				var answersInputs = this.dom.children('.answers').find('input');
				if((answersInputs.length - 2) == answersInputs.filter('['+classes.disabled+']').length) {
					this.disableHelps(helpType);
				}
			}
		},
		
		setSolved : function() {
			this.disableAnswer();
			this.disableHelps();
			this.solved = true;
		}
		
	});
	
});