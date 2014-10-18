$(document).ready(function() {
	
	/**
	 * Load Project Data
	 * 
	 * Parses the data in E.project.load and creates all the game elements with it.
	 * In the end, the E.project.load is deleted.
	 */
	E.load.project = function() {
		var game = E.game, data = E.project.load;

		// Load Project Status Function
		var loadStatus = function(data, game, progress) {
			var explorer = $('#'+ids.explorer);
			
			if('scenario' in data) {
				var currentScenario = explorer.find('ul.scenarios #'+data.scenario);

				if('element' in data) {
					E.sections.explorer.showItem(currentScenario.find('#'+data.element), true);
				}
				else {
					currentScenario.children('.'+classes.item).click();
				}
			}
			else if('property' in data) {
				var list = explorer.find('ul.properties > li');
				list.children('.arrow').click();
				list.children('ul').children('.'+data.property).click();
			}
			else if('screen' in data) {
				var list = explorer.find('ul.screens > li');
				list.children('.arrow').click();
				list.children('ul').children('.'+data.screen).click();
			}

			// Increment Progress Bar
			progress.increment();
		};
		
		var loadProperties = function(data, game, progress) {
			var defaults = E.defaults, properties = defaults.properties, resources = defaults.resources;

			// Get Logo
			var logo = data[properties[0]];
			game.logo = new game.Logo(logo.styles);
			game.logo.setIcon(properties[0]);


			// Get Scores
			var scores = game.scores = new game.Scores(), list = data[properties[1]];
			scores.setIcon(properties[1]);
			for(var key in list) {
				var score = list[key];

				if(key == 'team') {
					scores.setTeamScore(score.name, score.log);
				}
				else {
					scores.addPlayerScore(key, score.name, score.log);
				}
			}

			// Get Helps
			var helps = data[properties[2]];
			scores.setHelps(helps.name, helps.value, helps.log);

			// Get Sync
			var sync = data[properties[5]];
			game.setSync(sync.value);

			// Get Players
			var players = data[properties[3]], id = defaults.ids[properties[3]], name = E.strings.labels.playerName, info = [], defaultMaxPlayers = defaults.maximum.players;
			for(var i = 0; i < defaultMaxPlayers; i++) {
				info.push({ id: id + (i+1), name : name, image : resources['avatar'+i].object });
			}

			game.players = new game.Players(info, players.styles);
			game.players.createDOM();
			if(defaultMaxPlayers > 1) {
				game.players.setCollaborating(id + 2);
			}
			game.players.setPlayersNumber(players.max);
			game.players.setIcon(properties[3]);
			game.players.show();

			
			// Get Sounds
			var sounds = game.sounds = new game.Sounds(), list = data[properties[4]];
			for(var key in list) {
				sounds.setSourceId(list[key], key);
			}
			sounds.setIcon(properties[4]);

			// Increment Progress Bar
			progress.increment();
		};

		var loadScenarios = function(data, game, progress) {
			var html = E.html, list = data.list;

			for(var i = 0; i < list.length; i++) {
				var scenario = list[i],
					id = scenario.id,
					name = scenario.name,
					type = scenario.type,
					contentsByID = {},
					contentsByIndex = scenario.contents,
					toLoad = E.scenarios.createToLoadList(type);

				// Create ContentsByID and toLoad lists
				for(var j = 0; j < contentsByIndex.length; j++) {
					var elementId = contentsByIndex[j].id, collection = contentsByIndex[j].collection;

					// Store Resources to Load on each Draw
					E.scenarios.addToLoadList(toLoad, collection, game[collection][elementId]);
					contentsByID[elementId] = contentsByIndex[j];
				}

				// Create Scenario
				game.scenarios[i] = new game.Scenario(id, name, type, contentsByIndex, scenario.rules, scenario.jumps, scenario.styles);
				E.scenarios.setExtraProperties(game.scenarios[i], contentsByID, toLoad);
				html.createScenarioItem(id, name, type, contentsByIndex);

				// Increment Progress Bar
				progress.increment();
			}


			// Explorer Events
			E.sections.explorer.setItemsEvents('#'+ids.explorer+' .'+classes.list+' .scenarios > li');
		};

		var loadCollection = function(data, game, collection, progress) {
			var icons = classes.icons;

			delete data.count;
			for(var tag in data) {
				var elements = data[tag];
				for(var i = 0; i < elements.length; i++) {
					var item = elements[i], id = item.id, source = item.source, name = item.name, content = item.content, styles = item.styles, icon = tag;

					switch(tag) {
						case icons.heading:
							collection[id] = new game.Heading(id, name, content, styles);
							break;

						case icons.paragraph:
							collection[id] = new game.Paragraph(id, name, content, styles);
							break;

						case icons.button:
							collection[id] = new game.Button(id, name, item.type, content, styles);
							break;

						case icons.line:
							collection[id] = new game.Line(id, name, styles);
							break;

						case icons.square:
							collection[id] = new game.Square(id, name, content, styles);
							break;

						case icons.circle:
							collection[id] = new game.Circle(id, name, content, styles);
							break;

						case icons.balloon:
							collection[id] = new game.Balloon(id, name, content, styles);
							break;

						case icons.audio:
							collection[id] = new game.Audio(id, name, source, styles);
							break;

						case icons.image:
							collection[id] = new game.Image(id, name, source, styles);
							break;

						case icons.video:
							collection[id] = new game.Video(id, name, source, styles);
							break;

						case icons.pdf:
							collection[id] = new game.PDF(id, name, source, styles);
							break;

						case icons.question:
							collection[id] = new game.Question(id, name, source, item.group, item.scores, item.helps, styles);
							icon = item.group? icons.group : icons.question;
							break;
					}

					E.elements.setExtraProperties(collection[id], item.locked, icon);

					// Increment Progress Bar
					progress.increment();
				}
			}
		};
		
		// Initialize the Game
		game.init();
		game.setScenario(null, E.defaults.screens.game);

		// Load Game Start
		game.start = data.start;
		
		// Load Default Resources
		game.utils.loadResources(E.strings.loading.resources, E.defaults.resources, function() {
			var collections = E.defaults.collections;

			// Load Collections Counts
			var sum = 2;
			for(var i = 0; i < collections.length; i++) {
				var collection = collections[i];

				// Set collection count
				game[collection].count = data[collection].count;
				delete data[collection].count;

				// Set number of elements to load
				for(var element in data[collection]) {
					sum+= data[collection][element].length;
				}
			}

			var progress = new E.game.Loading(E.strings.loading.project, sum);

			// Load Properties
			loadProperties(data.properties, game, progress);

			// Load Collections
			for(var i = 1; i < collections.length; i++) {
				var collection = collections[i];
				loadCollection(data[collection], game, game[collection], progress);
			}

			// Load Scenarios
			loadScenarios(data[collections[0]], game, progress);

			// Load Project Status
			loadStatus(data.status, game, progress);
			
			if(progress.isFinished()) {
				E.sections.get().children('.'+classes.list).removeClass('ajax');
				E.sections.canvas.layerClick();
				delete E.project.load;
			}
		});
	}
	
	
	/**
	 * Saves the Project
	 * 
	 * Reads the E.project.save contents and cretes an object with the data to be sent to the server.
	 * While waiting for a response the save tool is blocked.
	 * In the end, the E.project.save data is deleted and it will be created again the next time E.tools.setSaveData is invoked.
	 */
	E.tools.save = function() {
		var tool = $('#'+ids.toolbar+' ul .save'),
			game = E.game, 
			saves = E.defaults.saves, 
			collections = E.defaults.collections, 
			properties = E.defaults.properties, 
			toSave = E.project.save, 
			toSend = {},
			oldSaveData = E.utils.cloneObject(toSave);
			
		// Disable tool and set ajax loading
		tool.disable();
		tool.addClass('ajax');
		
		// Set game start
		toSend.start = game.start;
		
		// Set status
		toSend.status = {};
		if(currentGame.element instanceof game.Scenario) {
			toSend.status.scenario = currentGame.element.getId();
		}
		else if(currentGame.element.parent == 'element') {
			toSend.status.scenario = currentGame.scenario.getId();
			toSend.status.element = { id: currentGame.element.getId(), collection: currentProject.element.parent('ul').parent('li').attr('class') };
		}
		else if(currentGame.element.parent == 'property') {
			toSend.status.property = currentGame.element.getIcon();
		}
		else if(currentGame.element.parent == 'screen') {
			toSend.status.screen = currentGame.element.icon;
		}
		
		if(typeof toSave != 'undefined') {
			
			// Set properties
			if(!$.isEmptyObject(toSave.properties)) {
				var currentToSave = toSave.properties, currentToSend = toSend.properties = {};
				for(var name in currentToSave) {
					var info = currentToSave[name], property = game[name], all = false;
					currentToSend[name] = {};

					switch(name) {
						case properties[0]: // Logo
							currentToSend[name].styles = property.styles;
							break;

						case properties[1]: // Scores
							currentToSend[name] = $.extend({}, { team: property.team }, property.players);
							currentToSend[properties[2]] = property.helps;
							break;

						case properties[3]: // Players
							currentToSend[name] = { max: property.max, styles: property.styles };
							break;

						case properties[4]: // Sounds
							for(var i = 0; i < property.elements.length; i++) {
								var sound = property.elements[i];
								currentToSend[name][sound] = property[sound].id;
							}
							break;
					}
				}
			}

			// Set scenarios
			var currentCollection = collections[0], scenarios = game.scenarios;
			currentToSave = toSave[currentCollection], currentToSend = toSend[currentCollection] = {};
			currentToSend.count = scenarios.count;
			for(var i = 0; i < scenarios.length; i++) {
				var scenario = scenarios[i], 
					info = (typeof currentToSave[scenario.id] != 'undefined')? currentToSave[scenario.id] : false;

				// Add scenario to the list
				currentToSend[i] = { id: scenario.id };

				if(info) {
					var all = false;
					currentToSend[i].type = scenario.type;
					
					if(typeof info[saves[0]] != 'undefined') {

						// Rules and Jumps
						currentToSend[i].rules = scenario.rules;
						currentToSend[i].jumps = scenario.jumps;

						all = true;
					}

					// Name
					if(all || typeof info[saves[1]] != 'undefined') {
						currentToSend[i][saves[1]] = scenario[saves[1]];
					}

					// Contents
					if(all || typeof info[saves[5]] != 'undefined') {
						currentToSend[i].contents = {};
						
						var contents = scenario.contents;
						for(var j = 0; j < contents.length; j++) {
							var currentContent = currentToSend[i].contents[j] = { id: contents[j].id, collection: contents[j].collection }

							if(all || typeof info[saves[5]][currentContent.id] != 'undefined') {
								currentContent[saves[5]] = contents[j][saves[5]];
							}
						}
					}

					// Styles
					if(all || typeof info[saves[6]] != 'undefined') {
						currentToSend[i][saves[6]] = scenario[saves[6]];
					}
				}
				
				delete currentToSave[scenario.id];
			}
			
			// Set removed scenarios
			if(!$.isEmptyObject(currentToSave)) {
				currentToSend[saves[7]] = [];
				for(var id in currentToSave) {
					if(typeof currentToSave[id][saves[7]] != 'undefined') {
						currentToSend[saves[7]].push(id);
					}
				}
			}
			
			// Set Collections
			for(var i = 1; i < collections.length; i++) {
				currentCollection = game[collections[i]], currentToSave = toSave[collections[i]], currentToSend = toSend[collections[i]] = {};
				
				currentToSend.count = currentCollection.count;
				
				for(var id in currentToSave) {
					var info = currentToSave[id];
					
					if(typeof info[saves[7]] == 'undefined') {
						var all = false, element = currentCollection[id];
						
						currentToSend[id] = { icon : element.icon };
						if(typeof info[saves[0]] != 'undefined') {

							if(element instanceof game.Button) {
								currentToSend[id].type = element.type;
							}
							else if(element instanceof game.Question) {
								if(typeof element.type != 'undefined') {
									currentToSend[id].type = element.type;
								}
								currentToSend[id].scores = element.scores;
							}

							all = true;
						}
						
						// Name
						if(all || typeof info[saves[1]] != 'undefined') {
							currentToSend[id][saves[1]] = element[saves[1]];
						}
						
						// Text
						if((all && typeof element[saves[2]] != 'undefined') || typeof info[saves[2]] != 'undefined') {
							currentToSend[id][saves[2]] = element[saves[2]];
						}
						
						// Source
						if((all && typeof element[saves[3]] != 'undefined') || typeof info[saves[3]] != 'undefined') {
							currentToSend[id][saves[3]] = element[saves[3]];
							
							if(element instanceof game.Question) {
								currentToSend[id].group = element.group;
								currentToSend[id].helps = element.helps;
							}
						}
						
						// Locked
						if(all || typeof info[saves[4]] != 'undefined') {
							currentToSend[id][saves[4]] = element[saves[4]];
						}
						
						// Styles
						if(all || typeof info[saves[6]] != 'undefined') {
							currentToSend[id][saves[6]] = element[saves[6]];
						}
						
					}
					else {
						// Removed
						currentToSend[id] = false;
					}
				}
			}
			
			currentProject.saving = true;
			delete E.project.save;
		}
		
		// Send the data
		var failed = true;
		E.ajax.request(
			'post', 
			E.system.server + 'projects/save/'+E.project.id, 
			{ save: JSON.stringify(toSend) }, 
			'text', true, true, 
			function(data) {
				if(data.length > 0) {
					E.modal.open($('#'+ids.modal+'_content'), data);
				}
				else failed = false;
			}, 
			function() {
				if(failed) {
					if(typeof E.project.save == 'undefined') {
						E.project.save = oldSaveData;
					}
					else $.extend(E.project.save, oldSaveData);
				}
				tool.enable();
				tool.removeClass('ajax');
				currentProject.saving = false;
			}
		);
	};
	
});