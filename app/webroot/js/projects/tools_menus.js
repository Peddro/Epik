$(document).ready(function() {
	
	/**
	 * Tools functions
	 *
	 * @package E.tools
	 * @author Bruno Sampaio
	 */
	E.tools = {
		
		/**
		 * Enables the tools contained on 'list' allowed for the selected 'element'.
		 *
		 * This method starts by disabling all tools in 'list' and then enable the general tools for any element (new, open, import, export, info, user, and settings).
		 * If the project has unsaved progress it also enables the save tool.
		 * If 'element' is an object that inherits E.game.Element, the function enableElementTools is executed.
		 * If 'element' is an object that inherits E.game.Property, the function enablePropertyTools is executed.
		 *
		 * @param DOM list - the tools container.
		 * @param mixed - the game element or property.
		 */
		enable : function(list, element) {
			var properties = E.defaults.properties;
			
			// Disable All
			list.children(':not(.logo-extended, .separator)').disable();
			
			// Enable Default Actions
			list.children('.new, .open, .import, .export, .zoom-in, .zoom-out, .info, .user, .settings').enable();
			
			// Enable Save
			if(!currentProject.saving) {
				list.children('.save').enable();
			}

			// If a property is selected
			if(element.parent == 'property') {
				this.enablePropertyTools(list, element);
			}
			
			// If a screen is selected
			else if(element.parent == 'screen') {
				this.enableScreenTools(list, element);
			}
			
			// If a element is selected
			else if(element.parent == 'element') {
				this.enableElementTools(list, element);
			}
			
		},
		
		
		/**
		 * Enables the tools contained on 'list' allowed for a certain game 'property' type.
		 * 
		 * This method may be used in the future to enable/disable tools for each property type.
		 * 
		 * @param DOM list - the tools container.
		 * @param mixed - the game property.
		 */
		enablePropertyTools : function(list, element) {},
		
		
		/**
		 * Enables the tools contained on 'list' allowed for a certain game 'screen' type.
		 * 
		 * This method may be used in the future to enable/disable tools for each screen type.
		 * 
		 * @param DOM list - the tools container.
		 * @param mixed - the game screen.
		 */
		enableScreenTools : function(list, element) {},
		
		
		/**
		 * Enables the tools contained on 'list' allowed for a certain game 'element' type.
		 *
		 * This method starts by enabling all the creation tools for an element.
		 * Then it enables the lock/unlock tool for the current element.
		 * And finally, based on the type of element it enables/disables the cut, copy, paste, and remove tools.
		 *
		 * @param DOM list - the tools container.
		 * @param mixed - the game element.
		 */
		enableElementTools : function(list, element) {
			var collections = E.defaults.collections;
			var isLectureScenario = (currentGame.scenario.type == E.defaults.types[collections[0]][0]);
			
			// Enable Creation Tools
			var tools = E.defaults.elementsByCollection, toEnable = '';
			for(var collection in tools) {
				if(!isLectureScenario || collection != collections[4]) {
					$.each(tools[collection], function(key, value) {
						if(value != classes.icons.button) {
							toEnable+= '.' + value + ',';
						}
					});
				}
			}
			
			// Enable/Disable Default for all Elements
			list.children(toEnable.substr(0, toEnable.length - 1)).enable();

			// Enable or Disable Lock/Unlock related Tools
			this.enableLockUnlock(list.children('.lock, .unlock'), element);
			
			// Enable/Disable Paste
			if(!$.isEmptyObject(currentProject.clipboard)) {
				list.children('.paste').enable();
			}
			else list.children('.paste').disable();
		},
		
		
		/**
		 * Enables Lock/Unlock Related Tools.
		 * 
		 * Tools affected by Lock/Unlock are Edit, Cut, Copy, and Remove.
		 * If 'element' is locked, the unlock tool is enabled and the related tools are disabled.
		 * If 'element' is unlocked, the lock tool is enabled and the related tools are only enabled if it isn't a button.
		 * 
		 * @param DOM tool - the lock/unlock tool.
		 * @param E.game.Element element - the element object.
		 */
		enableLockUnlock : function(tool, element) {
			var affectedTools = '.edit, .cut, .copy, .remove';
			if(!(element instanceof E.game.Scenario)) {
				
				// Enable Lock/Unlock and Enable/Disabled Related Tools
				if(element.isLocked()) {
					tool.removeClass('lock').addClass('unlock');
					tool.siblings(affectedTools).disable();
				}
				else {
					tool.removeClass('unlock').addClass('lock');
					
					if(element instanceof E.game.Button) {
						tool.siblings(affectedTools).disable();
					}
					else tool.siblings(affectedTools).enable();
				}
				tool.enable();
			}
			else {
				tool.siblings(affectedTools).enable();
				tool.disable();
			}
		},
		
		
		/**
		 * Sets the Data to be Saved
		 *
		 * This method stores on E.project.save the actions that need to be saved for each changed element since last save.
		 * The index parameter can take the followinf values:
		 * 		0 => 'all' (when all element data was changed);
		 * 		1 => 'name', 2 => 'content', 3 => 'source', 4 => 'lock' (when the element name, text content, source id, or locked status are changed);
		 * 		5 => 'position' (when the element position is changed);
		 * 		6 => 'styles' (when the element styles are changed);
		 * 		7 => 'removed' (when the element is removed);
		 * 
		 * @param string id - the changed element id (for properties it's their icon).
		 * @param string collection - the changed element collection (for properties it's 'properties').
		 * @param int index - the index for the action type to be saved.
		 * @param mixed extra - used to pass different kinds of information related with the action type (for position change it contains the changed element id).
		 */
		setSaveData : function(id, collection, index, extra) {
			if(!collection) collection = 'properties';
			
			// Create the save object if it is empty
			if(typeof E.project.save == 'undefined') {
				collections = E.defaults.collections;
				var save = E.project.save = { 'properties': {} };
				
				for(var i = 0; i < collections.length; i++) {
					save[collections[i]] = {};
				}
			}
			
			// Set operation to be saved
			if(id && collection) {
				var actions = E.defaults.saves, action = actions[index];
				var list = E.project.save[collection];
				if(typeof list[id] == 'undefined') list[id] = {};
				var toSave = list[id];
				
				if((typeof toSave[actions[0]] == 'undefined' && typeof toSave[actions[7]] == 'undefined') || action == actions[7]) {
					switch(action) {
						case actions[0]: case actions[7]:
							list[id] = {};
							list[id][action] = true;
							break;

						case actions[5]:
							if(typeof toSave[action] == 'undefined') toSave[action] = {};
							toSave[action][extra] = true;
							break;

						default: 
							toSave[action] = true;
							break;
					}
				}
			}
		},
		
		
		/**
		 * Sets the Type of Element being Created
		 *
		 * This method is invoked when the tools to create, or choose a resource/activity are clicked.
		 * It sets the type of element being created on E.game.current.creating. 
		 * In case an element is being changed an id is also provided.
		 *
		 * @param string id - the element id (null when creating).
		 * @param string collectionName - the element collection (null for properties).
		 * @param string itemType - the element type.
		 * @param string propertyName - the property to which the item belongs.
		 */
		startInsert : function(id, collectionName, itemType, propertyName) {
			currentGame.creating = { element: itemType };
			
			if(id) currentGame.creating.id = id;
			if(collectionName) currentGame.creating.collection = collectionName;
			if(propertyName) currentGame.creating.property = propertyName;
		},
		
		
		/**
		 * Prepares the Data to Load and Insert/Change a Resource or Activity Element.
		 *
		 * This method receives a source id and if E.game.current.creating contains data, it uses it to create or modify a resource or activity element.
		 * If the element being created is a scenario it passes the source id to the E.scenarios.createFromTemplate function.
		 * If the element being created is loadable it sets the data to load and the request callbacks to create or modify the element based on the data received from the server.
		 * If the element isn't loadable it is created or changed immediately.
		 * To create or update an element the E.elements.createOrModify function is used.
		 * 
		 * @param int sourceId - the element source id.
		 */
		insertFromChosen : function(sourceId) {
			var creating = currentGame.creating;
			
			if(typeof creating != 'undefined') {
				var collection = creating.collection;
				
				// If is a scenario
				if(creating.element == classes.icons.scenario) {
					E.scenarios.createFromTemplate(sourceId);
				}
				else {
					
					// Create the list with the element to load
					var toLoad, afterActivities, afterResources, finished;
					var isProperty = typeof creating.property != 'undefined';
						
					// If is a image, activity, or property
					if(E.elements.isLoadable(creating.element) || isProperty) {
						toLoad = E.elements.createToLoadData(collection, creating.element, sourceId);
						
						afterActivities = function(data) {
							if(typeof data[sourceId] != 'undefined') {
								E.elements.createOrModifyFromChosen(creating.id, collection, creating.element, sourceId, data[sourceId]);
							}
						};

						afterResources = function(data) {
							if(typeof data[sourceId] != 'undefined') {
								if(!isProperty) {
									E.elements.createOrModifyFromChosen(creating.id, collection, creating.element, sourceId, data[sourceId]);
								}
								else if(creating.property == E.defaults.properties[4]) {
									var sounds = E.game.sounds, sound = data[sourceId].object;
									sounds.setSourceId(sourceId, creating.element);
									sounds.setFile(sound, creating.element);

									var label = $('#'+ids.properties+' form[action='+creating.property+'] label[for='+ids.fields.sounds+'-'+creating.element+']');
									if(label.length > 0) {
										label.siblings('audio').remove();
										label.parent().append(sound);
									}

									E.tools.setSaveData(creating.property, 'properties', 0);
								}
							}
						};
						
						finished = function() { delete E.game.current.creating; };
					}
					
					// If neither of the above
					else {
						finished = function() { 
							E.elements.createOrModifyFromChosen(creating.id, collection, creating.element, sourceId, creating.element);
							delete E.game.current.creating;
						}
					}
					
					// Start loading the element
					E.load.contents(toLoad, E.strings.loading.newElement, null, afterActivities, afterResources, finished);
				}
			}
		},
		
		
		/**
		 * Renames a Game Element
		 *
		 * This method replaces an element name on the explorer list by an input text field.
		 * This field can then be used to rename the element.
		 * When the form is submitted the element name is set and if it is the current element the name field on the properties form is also changed.
		 * Finally, it also sets the data to be saved.
		 * 
		 * @param DOM dom - the element dom inside explorer panel.
		 * @param object element - the scenario/element object.
		 */
		rename : function(dom, element) {
			$(document).click(); // Submits other opened edit forms
			
			// Expand Scenario and Collection if DOM element is hidden
			E.sections.explorer.showItem(dom, false);
			
			var self = this;
			var name = dom.find('.name'), value = name.text();
			name.html('<form>'+E.html.form.createTextInput(ids.fields.name, false, false, true, value)+'</form>');
			
			// On Form Submit
			name.children('form').submit(function(event) {
				event.preventDefault();
				
				// Set Name
				var value = name.find('#'+ids.fields.name).attr('value');
				if(value.length > 0) {
					element.setName(value);
					
					if(dom[0] == currentProject.element[0]) {
						$('#'+ids.properties+' #'+ids.fields.name).attr('value', value);
					}
				}
				
				// Replace Input Field by Element Name
				if(element.getName().length > 0) {
					name.html(element.getName());
					self.setSaveData(element.getId(), E.game.utils.getCollectionById(element.getId()), 1);
					if(dom.hasClass(classes.icons.scenario)) dom.click();
				}
				else {
					$(this).remove();
					self.remove(dom);
				}
			});
			
			// Prevent Element Selection by Clicking the Input
			name.find('input[type=text]').click(function(event) {
				event.stopPropagation();
			}).focus();
			
			// Submit form if document is clicked
			$(document).click(function(event) {
				name.children('form').submit();
			});
		},
		
		
		/* CLIPBOARD ACTIONS
		 -----------------------------------------------------------------------------------------------------------------------------*/
		
		/**
		 * Cut or Copy Element
		 *
		 * This method stores the 'element' data on the clipboard (E.project.current.clipboard), to be pasted later.
		 * It first clones the element data, and if the operation invoked was a cut the element is also removed.
		 * 
		 * @param DOM dom - the element dom inside explorer panel.
		 * @param object element - the scenario/element object.
		 * @param bool cut - determines if the invoked operation was a cut or copy.
		 */
		cutOrCopy : function(dom, element, cut) {
			var clipboard = currentProject.clipboard = {}, tool = $('#'+ids.toolbar+' > ul').children('.paste');
			tool.disable();
			
			// Store Element Data
			if(element instanceof E.game.Scenario) {
				clipboard.scenario = E.scenarios.clone(element);
				clipboard.elements = [];
				
				for(var id in element.contentsByID) {
					var reference = element.contentsByID[id];
					var scenarioElement = E.game[reference.collection][id];
					
					if(!(scenarioElement instanceof E.game.Button)) {
						clipboard.elements.push(E.elements.clone(E.game[reference.collection][id], reference));
					}
				}
			}
			else {
				var index = E.elements.getScenarioItem(dom).index();
				var reference = E.game.scenarios[index].contentsByID[element.getId()];
				clipboard.elements = [ E.elements.clone(element, reference) ];
			}
			
			// Remove Item if user is Cutting
			if(cut) this.remove(dom, true);
			
			// Enable paste
			tool.enable();
		},
		
		
		/**
		 * Pastes an Element
		 *
		 * This method uses the data contained on the clipboard (E.project.current.clipboard) to create a new scenario/element.
		 * If there are loadable elements they are loaded before pasting them.
		 * 
		 * @param E.game.Scenario dom - the scenario where to paste the new element.
		 */
		paste : function(scenario) {
			var clipboard = currentProject.clipboard, 
				name = E.defaults.names, 
				collections = E.defaults.collections,
				icons = classes.icons,
				noselect = false,
				toLoad;
			
			// Paste Scenario
			if(typeof clipboard.scenario != 'undefined') {
				var clone = E.utils.cloneObject(clipboard.scenario);
				scenario = E.scenarios.create(clone.type, false, clone.rules, clone.styles);
				toLoad = clone.toLoad;
				noselect = true;
			}
			
			// Paste Elements
			if(typeof clipboard.elements != 'undefined' && clipboard.elements.length > 0) {	
				if(!toLoad) {
					var element = clipboard.elements[0];
					toLoad = E.elements.createToLoadData(element.reference.collection, element.icon, element.source);
				}

				after = function(data, collection) {
					toLoad[collection] = data;
				}

				E.load.contents(toLoad, E.strings.loading.scenario, null, after, after, function() {

					for(var i = 0; i < clipboard.elements.length; i++) {
						var element = E.utils.cloneObject(clipboard.elements[i]), reference = element.reference, data = null;
						var isActivity = element.reference.collection == collections[4];
					
						// If isn't an activity element or if scenario allows activities
						if(!isActivity || E.scenarios.allowsActivities(scenario.type)) {
						
							// Pasting a Button
							if(element.icon == icons.button) {
								data = element.type;
							}

							// Pasting loadable element
							if(E.elements.isLoadable(element.icon) && element.source) {
								if(element.source in toLoad[reference.collection]) {
									data = toLoad[reference.collection][element.source];
								}
								else element.source = 0;
							}

							E.elements.create(scenario, reference.collection, element.icon, false, element.text, element.source, data, element.styles, reference.position, noselect);
						}
						else {
							alert(E.strings.errors.notAllowed);
						}
					}
				});
			}
			
		},
		
		
		/**
		 * Locks or Unlocks an Element
		 * 
		 * This method first sets the element drag property to true/false.
		 * If this is the current element, it changes the image for lock/unlock tool on the toolbar,
		 * then it disables the form fields on the properties panel, and then it hides the anchors.
		 * Finally, it also sets the data to be saved.
		 *
		 * @param E.game.Element element - the element object.
		 */
		lockOrUnlock : function(element) {
			if(!(element instanceof E.game.Scenario)) {
				element.setLocked(!element.isLocked());
				
				if(element.getId() == currentGame.element.getId()) {
					this.enableLockUnlock($('#'+ids.toolbar+' ul').children('.lock, .unlock'), element);
					
					E.sections.properties.changeFormFieldsStatus(element.isLocked());
					
					//Show/Hide Anchors
					E.anchors.set(element);
				}
				
				this.setSaveData(element.getId(), E.game.utils.getCollectionById(element.getId()), 4);
			}
		},
		
		
		/**
		 *
		 */
		zoom : function(out) {
			if(!$.browser.msie && !$.browser.mozilla) {
				var game = E.game;
				if(typeof game.stage !== 'undefined') {
					var width = game.stage.getWidth();
					var height = game.stage.getHeight();
					var scale = E.utils.cloneObject(game.stage.getScale());

					if(out) {
						scale.x-= 0.1;
						scale.y-= 0.1;
					}
					else {
						scale.x+= 0.1;
						scale.y+= 0.1;
					}

					scale.x = Math.round(scale.x * 10)/10;
					scale.y = Math.round(scale.y * 10)/10;

					if(scale.x >= 0.5 && scale.x <= 1) {

						// Scale Stage
						game.stage.setScale(scale.x, scale.y);
						if(currentGame.scenario) {
							game.stage.draw();
						}

						// Scale Screen
						var dom = game.getScreen();
						dom.css({ width : (width * scale.x), height : (height * scale.y) });
						dom.children(':not(.scenario.game)').css('zoom', scale.x);
						dom.children('.scenario.game').find('.activity').css('zoom', scale.x);
						game.players.resize();

						// Center Screen Vertically
						E.game.utils.setScreenTopMargin();
					}
				}
			}
			else {
				alert(E.strings.alerts.mozillaZoom);
			}
		},
		
		
		/**
		 * Removes a Scenario or Element
		 *
		 * This method removes an element from the explorer panel and from the canvas.
		 * - For a scenario, it determines the scenario to be selected if this is the current one,
		 *   verifies if it is the game starting scenario, removes its contents,
		 *   removes it from other scenarios jumps arrays, and removes it from the select fields 
		 *   on the properties panel if other scenario is selected.
		 *
		 * - For an element, if it belongs to the current scenario, it is removed from the canvas,
		 *   then if it is the current element, its scenario is selected,
		 *   and then it is removed from the scenario contents and toLoad list (if is a loadable element).
		 *   
		 * Finally, for both cases it also sets the data to be saved.
		 * 
		 * @param DOM dom - the element dom inside explorer panel.
		 * @param object element - the scenario/element object.
		 */
		remove : function(element, force) {
			if(force || confirm(E.strings.alerts.remove)) {
				var game = E.game, toRemove = null;

				// Is removing a scenario
				if(element.hasClass(classes.icons.scenario)) {
					toRemove = element.parent('li');
					var	id = toRemove.attr('id'), index = toRemove.index(), total = toRemove.parent('ul').children().length;

					if(total > 1) {
						var scenarios = game.scenarios;
						
						if(typeof scenarios[index] != 'undefined') {
							
							// Remove from Scenarios Collection
							var scenario = scenarios[index];

							// Check if is Game Start
							if(id == E.game.start) E.game.start = false;

							// Remove Scenario Contents
							if(typeof scenario.contents != 'undefined') {
								var contents = scenario.contents;
								for(var i = 0; i < contents.length; i++) {
									var content = contents[i];
									delete game[content.collection][content.id];
									this.setSaveData(content.id, content.collection, 7);
								}
							}

							// Remove from other scenarios jump array
							for(var i = 0; i < scenarios.length; i++) {
								var jumps = scenarios[i].jumps;
								for(var j in jumps) {
									if(typeof jumps[j].to != 'undefined' && jumps[j].to == id) {
										E.sections.properties.addRemoveJumpButton(scenarios[i], j, false);
										scenarios[i].removeJump(j);
									}
								}
							}

							// Remove scenario option from properties form if a scenario is selected
							var selects = $('#'+ids.properties+' #'+ids.fields.flow.general+' select');
							selects.each(function(key, value) {
								var option = $(value).children('option[value='+id+']');
								if(option.length > 0) {
									if(typeof option.attr('selected') != 'undefined') {
										$(value).children(':first').attr('selected', true);
									}
									option.remove();
								}
							});
							
							// If is the selected scenario
							if(currentGame.scenario && id == currentGame.scenario.getId()) {
								if(index >= 1 && index < total) {
									toRemove.prev().children('.'+classes.icons.scenario).click();
								}
								else if(index == 0) {
									toRemove.next().children('.'+classes.icons.scenario).click();
								}	
							}

							// Set scenario as removed
							scenarios.splice(index, 1);
							this.setSaveData(scenario.getId(), E.defaults.collections[0], 7);
						}
						else {
							alert(E.strings.errors.corruptedItem);
							toRemove = null;
						}
					}
					else {
						alert(E.strings.errors.numOfScenarios);
						toRemove = null;
					}
				}

				// Is removing an element
				else {
					toRemove = element;
					
					var id = toRemove.attr('id'), 
						scenarioItem = toRemove.parents('.collections').parent('li'),
						scenario = game.scenarios[scenarioItem.index()],
						reference = scenario.contentsByID[id];
					
					// Remove form Collection and Scenario
					if(typeof reference != 'undefined') {
						var element = game[reference.collection][id];
						
						// If element belongs to current scenario
						if(currentGame.scenario && scenario.getId() == currentGame.scenario.getId()) {
							var shape = element.shape;
							var layer = shape.getLayer();
							
							// Remove Element DOM
							if(typeof element.dom != 'undefined') {
								element.dom.remove();
							}
							shape.destroy();
							
							// If is the selected element
							if(id == currentGame.element.getId()) {
								scenarioItem.children('.'+classes.icons.scenario).click();
							}
							else layer.draw();
						}
						
						// Remove it from scenario contents
						delete scenario.contentsByID[id];
						for(var i = 0; i < scenario.contents.length; i++) {
							if(id == scenario.contents[i].id) {
								scenario.contents.splice(i, 1);
								break;
							}
						}
						
						// Remove if from scenario toLoad
						if(reference.collection in scenario.toLoad) {
							var source = element.getSource();
							var toLoad = scenario.toLoad[reference.collection];
							if(source in toLoad) {
								toLoad = toLoad[source];
								if('list' in toLoad) toLoad = toLoad.list;
								
								for(var i = 0; i < toLoad.length; i++) {
									if(toLoad[i] == id) {
										toLoad.splice(i, 1);
										break;
									}
								}

								if(toLoad.length == 0) delete scenario.toLoad[reference.collection][source];
							}
						}
						
						// Remove it from collection
						delete game[reference.collection][id];
						
						// Set the element position as changed on the scenario and set it as removed from collection.
						this.setSaveData(id, reference.collection, 7);
						this.setSaveData(scenario.getId(), E.defaults.collections[0], 5, id);
					}
					else {
						alert(E.strings.errors.corruptedItem);
						toRemove = null;
					}
				}

				// Remove from Document - in case it is a scenario it is removed only if there are more
				if(toRemove != null) {
					toRemove.fadeOut('fast', function() {
						toRemove.remove();
					});
				}
			}
		},
		
		
		/**
		 * Binds the Toolbar Tools Events
		 */
		events : function() {
			var self = this, elements = E.elements, collections = E.defaults.collections;
			
			E.utils.setTipTips($('#'+ids.toolbar+' .tool'), '350px', 'bottom');
			
			$('#'+ids.toolbar+' .tool, '+'#'+ids.menus+' .menu:not(.user)').click(function(event) {
				event.preventDefault();
				event.stopPropagation();
			});
			
			var setTool = function(name, collection, action) {
				$('#'+ids.toolbar+' .'+name).click(function(event) {
					if(!$(this).isDisabled()) action(name, collection);
				});
			}

			// Save
			setTool('save', false, function() {
				self.save();
			});

			// Cut
			setTool('cut', false, function() { 
				self.cutOrCopy(currentProject.element, currentGame.element, true);
			});

			// Copy
			setTool('copy', false, function() { 
				self.cutOrCopy(currentProject.element, currentGame.element, false);
			});

			// Paste
			setTool('paste', false, function() { 
				self.paste(currentGame.scenario);
			});

			// Lock
			setTool('lock', false, function() { 
				self.lockOrUnlock(currentGame.element);
			});
			
			// Unlock
			setTool('unlock', false, function() { 
				self.lockOrUnlock(currentGame.element);
			});

			// Remove
			setTool('remove', false, function() { 
				self.remove(currentProject.element);
			});
			
			// Zoom In
			setTool('zoom-in', false, function() { 
				self.zoom(false);
			});

			// Zoom Out
			setTool('zoom-out', false, function() { 
				self.zoom(true);
			});

			// Next
			setTool('next', false, function() {});

			// Previous
			setTool('previous', false, function() {});

			// Play
			setTool('play', false, function() {});
			
			// Insert Elements
			var tools = E.defaults.elementsByCollection;
			for(var collection in tools) {
				var elementsList = tools[collection];
				var hasSource = (collection == collections[3] || collection == collections[4]);
				
				$.each(elementsList, function(key, value) {
					if(value != classes.icons.button) {
						var callback;
						if(hasSource) {
							callback = function(elementType, collectionName) {
								self.startInsert(null, collectionName, elementType); 
							}
						}
						else {
							callback = function(elementType, collectionName) { 
								elements.createNew(collectionName, elementType);
							}
						}
						setTool(value, collection, callback);
					}
				});
			}
			
			
			// Set Tools Keyboard Shortcuts
			this.shortcuts();
		},
		
		
		/**
		 * Binds the Keyboard Shortcuts Events
		 */
		shortcuts: function() {
			var mainKey = E.tmp.mainKey;
			delete E.tmp.mainKey;
			
			var isFormFieldFocused = function() {
				return $('input, select, textarea').is(':focus');
			};
			
			var createShortcut = function(keys, test, section, tool, subtool) {
				subtool = subtool? ' .'+subtool : '';
				$(document).bind('keydown', keys, function(event) {
					if(!test || (test && !isFormFieldFocused())) {
						event.preventDefault();
						$('#'+section+' .' + tool + subtool).click();
					}
				});
			};
			
			var createClipboardShortcut = function(name) {
				$(document).bind(name, function(event) {
					if(!isFormFieldFocused() && !$('#'+ids.modal).is(':visible')) {
						event.preventDefault();
						$('#'+ids.toolbar+' .'+name).click();
					}
				});
			};
			
			// New Project
			createShortcut('Alt+'+mainKey+'+p', false, ids.menus, 'new', 'project');
			
			// New Activity
			createShortcut('Alt+'+mainKey+'+a', false, ids.menus, 'new', 'activity');
			
			// New Resource
			createShortcut('Alt+'+mainKey+'+r', false, ids.menus, 'new', 'resource');
			
			// Open Project
			createShortcut(mainKey+'+o', false, ids.toolbar, 'open');
			
			// Save Project
			createShortcut(mainKey+'+s', false, ids.toolbar, 'save');
			
			// Cut
			createClipboardShortcut('cut');
			
			// Copy
			createClipboardShortcut('copy');
			
			// Paste
			createClipboardShortcut('paste');
			
			// Lock
			createShortcut(mainKey+'+l', false, ids.toolbar, 'lock');
			
			// Unlock
			createShortcut(mainKey+'+u', false, ids.toolbar, 'unlock');
			
			// Remove
			createShortcut(mainKey+'+Backspace', false, ids.toolbar, 'remove');
			
			// Zoom In
			createShortcut(mainKey+'+up', false, ids.toolbar, 'zoom-in');
			
			// Zoom out
			createShortcut(mainKey+'+down', false, ids.toolbar, 'zoom-out');
			
			// Previous
			createShortcut(mainKey+'+z', true, ids.toolbar, 'previous');
			
			// Next
			createShortcut(mainKey+'+Shift+z', true, ids.toolbar, 'next');
			
			// Play
			createShortcut(mainKey+'+return', false, ids.toolbar, 'play');
			
			var createMoveShortcut = function(event, axis, value) {
				if(!isFormFieldFocused() && currentGame.scenario && currentGame.element) {
					event.preventDefault();
					
					if(currentGame.element instanceof E.game.Scenario) {
						
						// Expand/Collapse Arrow
						if(axis == 'x') {
							var arrow = currentProject.scenario.children('.arrow'), isExpanded = arrow.hasClass('expanded');
							if((value > 0 && !isExpanded) || (value < 0 && isExpanded)) {
								arrow.click();
							}
						}
						
						// Move through Scenarios
						else if(axis == 'y') {
							var index = currentProject.scenario.index(), total = currentProject.scenario.parent('ul').children().length, select;
							
							if(total > 1) {
								if(index > 0 && value < 0) select = currentProject.scenario.prev();
								else if(index < total && value > 0) select = currentProject.scenario.next();
							}
							
							// Select scenario
							if(select) select.children('.'+classes.icons.scenario).click();
						}
					}
					else {
						
						// Set new position
						var form = $('#'+ids.properties+' form'), shape = currentGame.element.shape, position = currentGame.scenario.contentsByID[currentGame.element.getId()].position;
						var absolutePosition = shape.getAbsolutePosition();
						absolutePosition[axis]+= value;
						shape.setAbsolutePosition(absolutePosition.x, absolutePosition.y);

						// Set the values in the properties form
						E.sections.properties.switchPositionType(form, 'absolute', shape, position, ids.fields);

						// Refresh the layer
						var layer = shape.getLayer();
						E.game.utils.setShapePosition(layer, currentGame.element, position);
						E.anchors.move(shape, true);
						layer.draw();
					}
				}
			};
			
			$(document).bind('keydown', 'up', function(event) {
				createMoveShortcut(event, 'y', -1);
			});

			$(document).bind('keydown', 'down', function(event) {
				createMoveShortcut(event, 'y', 1);
			});

			$(document).bind('keydown', 'left', function(event) {
				createMoveShortcut(event, 'x', -1);
			});

			$(document).bind('keydown', 'right', function(event) {
				createMoveShortcut(event, 'x', 1);
			});
		}
		
	};
	
	
	/**
	 * Tools Menus functions
	 *
	 * @package E.menus
	 * @author Bruno Sampaio
	 */
	E.menus = {
		
		/**
		 * Binds the General Menus Events
		 */
		events : function() {
			this.openClose();
			this.toolbarMenusEvents();
			this.explorerMenuEvents();
		},
		
		
		/**
		 * Opens/closes the 'tools' menus.
		 * The tools must be inside '#toolbar', they must have class '.expand' and their menus must be inside '#menus'.
		 * Both the tools and the correspondent menus must have a class with the same name.
		 * The tool name must be the class on third position.
		 */
		openClose : function() {
			
			$('#'+ids.toolbar+' .tool.expand').click(function(event) {
				if(!$(this).isDisabled()) {
					var menu = $('#'+ids.menus).find('.'+$(this).attr("class").split(/\s/)[2]);

					if(!$(this).hasClass('selected')) {
						$(document).click();

						currentProject.tool = $(this);
						currentProject.menu = menu;

						var windowWidth = $(window).width();
						E.menus.setToolbarMenuPosition(currentProject.tool, currentProject.menu, windowWidth);

						currentProject.menu.fadeIn('fast');

						currentProject.tool.addClass(classes.selected);
					}
					else {

						currentProject.menu.fadeOut('fast');
						currentProject.tool.removeClass(classes.selected);

						currentProject.tool = $();
						currentProject.menu = $();
					}
				}
			});
		},
		
		
		/**
		 * Calculates 'menu' position in relation to its respective 'tool' and the containerWidth.
		 *
		 * @param DOM tool - tool element.
		 * @param DOM menu - menu element.
		 * @param int containerWidth - the tool and menu container which usually is the browser window.
		 */
		setToolbarMenuPosition : function(tool, menu, containerWidth) {
			if(tool.length > 0) {
				var toolOffset = tool.offset();

				if(toolOffset.left < containerWidth/2) {
					menu.addClass('anchor-left');
					menu.removeClass('anchor-right');
					menu.css('left', toolOffset.left);
				}
				else {
					menu.addClass('anchor-right');
					menu.removeClass('anchor-left');
					menu.css('right', containerWidth-(toolOffset.left + tool.outerWidth()));
				}

				menu.css('top', toolOffset.top + tool.outerHeight() - 1);
			}
		},
		
		
		/**
		 * Shows the Right Click Menu.
		 *
		 * This method receives the dom right clicked and the point clicked.
		 * The menu will be shown with its top or bottom left corner anchored to the specified point.
		 * 
		 * @param DOM dom - the right clicked dom.
		 * @param object point - the event object with point data.
		 */
		showRightClickMenu : function(dom, point) {
			currentProject.rightClicked = dom;
			
			if(currentProject.rightClicked.length > 0) {
				var menu = $('#'+ids.explorer+' > .'+classes.elementMenu);
				
				// Set Clicked Item
				if(currentProject.rightClicked.hasClass(classes.icons.scenario)) {
					var index = currentProject.rightClicked.parent('li').index();
					currentGame.rightClicked = E.game.scenarios[index];
				}
				else {
					var id = currentProject.rightClicked.attr('id');
					var collection = currentProject.rightClicked.parent('ul').parent('li').attr('class');
					currentGame.rightClicked = E.game[collection][id];
				}
				
				// Enable Tools for Clicked Item
				E.tools.enable(menu.children('.'+classes.list), currentGame.rightClicked);
				
				// Show Menu
				var y = point.pageY, windowHeight = $(window).height();
				menu.css({ visibility : 'hidden' });
				if(point.pageY + menu.height() > windowHeight) {
					y =  y - menu.height();
				}
				menu.hide().css({ top : y, left : point.pageX, visibility : '' }).show('fast');
			}
		},
		
		
		/**
		 * Binds the Toolbar Menus Tools Events
		 */
		toolbarMenusEvents : function() {
			
			var setMenuTool = function(menu, name, action, params) {
				var tool = $('#'+ids.toolbar+' .'+menu);
				
				$('#'+ids.menus+' .'+menu+' .'+name).click(function(event) {
					if(!tool.isDisabled()) action(params);
				});
			}
			
			// New Scenario
			var scenariosTypes = E.defaults.types[E.defaults.collections[0]];
			for(var i = 0; i < scenariosTypes.length; i++) {
				var scenarioType = scenariosTypes[i];
				setMenuTool('new', classes.icons.scenario+'.'+scenarioType, function(type) {
					E.scenarios.create(type);
				}, scenarioType);
			}
			
			// New Scenario from Template
			setMenuTool('new', classes.icons.scenario+'.template', function() {
				E.tools.startInsert(null, null, classes.icons.scenario);
			});
			
			E.utils.setTipTips($('#'+ids.menus+' > .menu > .'+classes.list+' > .'+classes.item+'[title]'), '300px', 'right');
			
		},
		
		
		/**
		 * Binds the Right Click Menu Tools Events
		 */
		explorerMenuEvents : function() {
			var menu = $('#'+ids.explorer+' > .'+classes.elementMenu);

			// Click on menu
			menu.click(function(event) {
				event.preventDefault();
				event.stopPropagation();
			});

			// Options Click
			var menuOptionsList = menu.children('.'+classes.list);
			menuOptionsList.children().click(function() {
				menu.hide();
			});

			// Rename Option
			menuOptionsList.children('.edit').click(function(event) {
				if(!$(this).isDisabled()) {
					E.tools.rename(currentProject.rightClicked, currentGame.rightClicked);
				}
			});

			// Cut
			menuOptionsList.children('.cut').click(function(event) {
				if(!$(this).isDisabled()) {
					E.tools.cutOrCopy(currentProject.rightClicked, currentGame.rightClicked, true);
				}
			});

			// Copy
			menuOptionsList.children('.copy').click(function(event) {
				if(!$(this).isDisabled()) {
					E.tools.cutOrCopy(currentProject.rightClicked, currentGame.rightClicked, false);
				}
			});

			// Paste
			menuOptionsList.children('.paste').click(function(event) {
				if(!$(this).isDisabled()) {
					var element = currentGame.rightClicked;
					if(!(element instanceof E.game.Scenario)) {
						var index = E.elements.getScenarioItem(currentProject.rightClicked).index();
						element = E.game.scenarios[index];
					}
					
					E.tools.paste(element);
				}
			});

			// Lock/Unlock
			menuOptionsList.children('.lock, .unlock').click(function(event) {
				if(!$(this).isDisabled()) {
					E.tools.lockOrUnlock(currentGame.rightClicked);
				}
			});

			// Delete Option
			menuOptionsList.children('.remove').click(function(event) {
				if(!$(this).isDisabled()) {
					E.tools.remove(currentProject.rightClicked);
				}
			});
		}
		
	}
	
});