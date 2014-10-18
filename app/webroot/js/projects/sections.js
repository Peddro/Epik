$(document).ready(function() {
	
	/**
	 * Sections functions
	 *
	 * @package E.sections
	 * @author Bruno Sampaio
	 */
	E.sections = {
		
		slideTime: 400, // The sections slide time when expanding or collapsing.
		sectionsCollapsed: 0, // The number of sections collapsed (min = 0, max = 2).
		
		
		/**
		 * Binds Sections General Events
		 *
		 * Removes the links default behaviour when clicked and stops propagation.
		 * Applies the target element to the links on the footer.
		 */
		generalEvents : function() {
			
			// Sections Events
			this.expandCollapse($('#'+ids.explorer), $('#'+ids.explorer+' .header .options .minimize-left'));
			this.expandCollapse($('#'+ids.properties), $('#'+ids.properties+' .header .options .minimize-right'));
			this.maximize($('#'+ids.canvas), $('#'+ids.canvas+' .header .options .maximize'));
			
			// Explorer Events
			this.explorer.selectItems('#'+ids.explorer+' .'+classes.list+' > ul > li > ul > li');
			this.explorer.expandCollapseItems('#'+ids.explorer+' .arrow');
			this.explorer.scenariosListSort();
			
			// Footer Links Events
			E.utils.setTargetLinks($('#'+ids.footer));
		},
		
		
		/**
		 * Get the specified section DOM Element or if no name specified gets all sections DOMs
		 *
		 * @param name - section name (optional)
		 * @return DOMElement
		 */
		get : function(name) {
			return $(name? '#'+name : '#'+ids.content+' > div > .column.section');
		},
		
		
		/**
		 * Expands or collapses a 'section' when 'button' is clicked.
		 *
		 * @param DOM section - section to expand or collapse.
		 * @param DOM button - button clicked.
		 */
		expandCollapse : function(section, button) {
			var self = this;
			
			button.children('a').click(function(event) {

				event.preventDefault();

				var header = section.children('.header'),
					canvas = $('#'+ids.canvas),
					utils = E.game.utils;

				// If section is collapsed.
				if(section.hasClass('collapsed')) {

					// Hide the header.
					header.fadeOut('fast');

					// Expand Section
					section.animate({'width' : section.data('width')}, self.slideTime);

					// Reduce Canvas
					if(button.hasClass('maximize-right')) {
						canvas.animate({'margin-left' : canvas.data('margin-left')});
						button.removeClass('maximize-right').addClass('minimize-left');
					}
					else if(button.hasClass('maximize-left')) {
						canvas.animate({'margin-right' : canvas.data('margin-right')});
						button.removeClass('maximize-left').addClass('minimize-right');
					}

					// Change section class and show all its contents again.
					section.removeClass('collapsed');
					header.outerWidth('');
					section.children('.header, .list').fadeIn('slow');

					// Decrement number of collapsed sections.
					self.sectionsCollapsed--;
				}
				else {

					// Hide all section childrens.
					section.children().fadeOut('fast');

					// Collapse Section
					section.data('width', section.outerWidth());
					section.animate({'width' : header.outerHeight()}, self.slideTime);

					// Expand Canvas
					if(button.hasClass('minimize-left')) {
						canvas.data('margin-left', parseInt(canvas.css('margin-left')));
						canvas.animate({'margin-left' : 58});
						button.removeClass('minimize-left').addClass('maximize-right');
					}
					else if(button.hasClass('minimize-right')) {
						canvas.data('margin-right', parseInt(canvas.css('margin-right')));
						canvas.animate({'margin-right' : 58});
						button.removeClass('minimize-right').addClass('maximize-left');
					}

					// Change section class and show header already rotated.
					section.addClass('collapsed');
					header.outerWidth(section.height());
					header.fadeIn('slow');

					// Increment number of collapsed sections.
					self.sectionsCollapsed++;
				}

				// Check if canvas button must be changed.
				var canvasButton = $('#'+ids.canvas+' .header .options .icon-small').parent();
				if(self.sectionsCollapsed >= 2) {
					canvasButton.removeClass('maximize');
					canvasButton.addClass('minimize');
				}
				else {
					canvasButton.removeClass('minimize');
					canvasButton.addClass('maximize');
				}
			});
			
			button.children().tipTip({maxWidth: '250px', defaultPosition: 'bottom'});
		},
		
		
		/**
		 * Maximizes the 'section' when 'button' is clicked.
		 *
		 * @param DOM section - section to maximize.
		 * @param DOM button - button clicked.
		 */
		maximize : function(section, button) {
			button.children('a').click(function() {
				
				if(button.hasClass('maximize')) {
					$('#'+ids.properties).find('.minimize-right a').click();
					$('#'+ids.explorer).find('.minimize-left a').click();

					button.removeClass('maximize').addClass('minimize');
				}
				else {
					$('#'+ids.properties).find('.maximize-left a').click();
					$('#'+ids.explorer).find('.maximize-right a').click();

					button.removeClass('minimize').addClass('maximize');
				}
			});
			
			button.children().tipTip({maxWidth: '250px', defaultPosition: 'bottom'});
		},
		
		
		/**
		 * Explorer Section functions
		 *
		 * @package E.sections
		 * @subpackage explorer
		 * @author Bruno Sampaio
		 */
		explorer: {
			
			/**
			 * Binds General Events to Explorer Items
			 *
			 * @param string items - selector string.
			 */
			setItemsEvents : function(items) {
				this.selectItems(items+' > .'+classes.item+', '+items+' .elements > .'+classes.item);
				this.expandCollapseItems(items+' > .arrow, '+items+' > .collections > li > .arrow');
				this.rightClickItems(items+' > .'+classes.item+', '+items+' .elements > .'+classes.item);
			},
			
			
			/**
			 * Binds the Select Event
			 * 
			 * Sets an item as selected, storing its DOM on E.project.current.element.
			 * If it is a scenario, its DOM is also stored on E.project.current.scenario and its object on E.game.current.scenario.
			 * If it is an element, its scenario is stored on the previously mentioned variables and its object is stored on E.game.current.element.
			 * If it is a property, its object is stored on E.game.current.element, and the scenario is set to null.
			 *
			 * If selected scenario, or property contain loadable elements they are loaded first.
			 * When everything is loaded, the element tools are enabled, the canvas elements are drawn and the properties form is set.
			 *
			 * @param string items - selector string.
			 */
			selectItems : function(items) {
				var self = this;
				
				$(items).click(function(event) {
					currentProject.element.removeClass('selected');
					$(this).addClass('selected');
					currentProject.element = $(this);
					
					var oldScenario = (currentGame.scenario != null)? currentGame.scenario.getId() : false, reference = null;
					var toLoad, message, before, afterResources, afterActivities, finished;
					var gscreen = E.defaults.screens.game, redraw = true;
					
					$('#'+ids.properties+' .'+classes.list).html('');
						
					// If current item selected is a scenario
					if(currentProject.element.hasClass(classes.icons.scenario)) {
						currentProject.scenario = currentProject.element.parent('li');
						currentGame.scenario = currentGame.element = E.game.scenarios[currentProject.scenario.index()];
					}
					
					// If current item selected is a element
					else if(currentProject.element.hasClass('element')) {
						var collectionDOM = currentProject.element.parent('.elements').parent('li');
						var collection = collectionDOM.attr('class');
						var elementId = currentProject.element.attr('id');
						
						currentProject.scenario = collectionDOM.parent().parent('li');
						currentGame.scenario = E.game.scenarios[currentProject.scenario.index()];
						currentGame.element = E.game[collection][elementId];
						reference = currentGame.scenario.contentsByID[elementId];
					}
					
					// If current item selected is a property
					else if(currentProject.element.hasClass('property')) {
						var property = currentProject.element.attr('class').split(' ')[2];
						
						currentProject.scenario = $();
						currentGame.scenario = null;
						currentGame.element = E.game[property];
						
						// Load Sounds Data
						if(property == E.defaults.properties[4]) {
							var resources = {}, sounds = currentGame.element.elements, defaultSounds = E.defaults.resources;
							
							// Create List of Sounds to Load
							for(var i = 0; i < sounds.length; i++) {
								var id = currentGame.element.getSourceId(sounds[i]), file = currentGame.element.getFile(sounds[i]);
								if(id && !file) {
									if(typeof resources[id] == 'undefined') resources[id] = [];
									resources[id].push(sounds[i]);
								}
								else if(!id) currentGame.element.setFile(defaultSounds[sounds[i]].object, sounds[i]);
							}
							
							// Load Sounds Files
							if(!$.isEmptyObject(resources)) {
								toLoad = { resources: resources };
								message = E.strings.loading.sounds;
								afterResources = function(data) {
									var errors = false;
									
									for(var source_id in resources) {
										var found = (source_id in data);
										var current = found? data[source_id].object : false;
										var resourcesList = resources[source_id];
										
										for(var i = 0; i < resourcesList.length; i++) {
											var soundType = resourcesList[i];
											if(current) {
												currentGame.element.setFile(current, soundType);
											}
											else {
												currentGame.element.setSourceId(0, soundType);
												E.tools.setSaveData(property, null, 0);
												errors = true;
											}
										}
									}
									
									if(errors) alert(E.strings.errors.notFound);
								};
							}
						}
						
						before = function() {
							E.game.utils.resetStage();
						};
						
						finished = function() {
							self.setSelected(currentGame.scenario, currentGame.element);
						};
					}
					
					// If current item selected is a screen
					else if(currentProject.element.hasClass('screen')) {
						gscreen = currentProject.element.attr('class').split(' ')[2];
						
						currentProject.scenario = $();
						currentGame.scenario = null;
						currentGame.element = { parent : 'screen', icon : gscreen };
						
						finished = function() {
							self.setSelected(currentGame.scenario, currentGame.element);
						};
					}
					
					
					// If old scenario is different of current scenario
					if(currentGame.scenario != null) {
						if(oldScenario != currentGame.scenario.getId()) {
							var collections = E.defaults.collections;
							
							toLoad = currentGame.scenario.toLoad;
							message = E.strings.loading.scenario;
							before = function() {
								E.game.utils.clearStage();
							};
							
							afterResources = afterActivities = function(data, collectionName) {
								var collection = E.game[collectionName];
								var isActivity = collectionName == collections[4];
								var errors = false;
								
								for(var source_id in toLoad[collectionName]) {
									var found = (source_id in data), ids = toLoad[collectionName][source_id];
									if(typeof ids.list != 'undefined') ids = ids.list;
									
									// Set Data for Current Source
									var current = found? data[source_id] : false;
									
									for(var i = 0; i < ids.length; i++) {
										var element = collection[ids[i]];
										
										// If data not found this element was probably removed from the database
										if(!found) {
											element.setSource(0);
											current = element.getIcon();
											E.tools.setSaveData(element.getId(), collectionName, 3);
											errors = true;
										}
										element.setData(current);
										
										if(isActivity) {
											E.elements.setActivityExtraProperties(current, element.helps);
										}
									}
								}
								
								if(errors) alert(E.strings.errors.notFound);
							};
							
							redraw = false;
						}
						
						finished = function() {
							self.setSelected(currentGame.scenario, currentGame.element, reference, redraw);
						};
					}
					
					// If must switch screen
					if(gscreen) {
						if(!E.game.getActiveScenario().hasClass(gscreen)) {
							E.game.setScenario(currentGame.scenario? true : false, gscreen);
						}
					}
					
					// Load current element contents and then draw it and set its properties form
					E.load.contents(toLoad, message, before, afterActivities, afterResources, finished);
				});
			},
			
			
			/**
			 * Binds the Event to Expand or Collapse an Explorer Item
			 *
			 * Used to expand or collapse a scenario/collection contents.
			 *
			 * @param string items - selector string.
			 */
			expandCollapseItems : function(items) {
				$(items).click(function(event) {
					var list = $(this).parent().children('ul');

					if(list.length > 0) {
						var collection = $(this).siblings('.collection');
						if(list.is(':visible')) {
							list.animate({ height: 0 }, E.sections.slideTime, function() {
								list.hide();
								if(collection.length > 0) collection.removeClass('opened').addClass('closed');
							});
							
							$(this).removeClass('expanded');
						}
						else {
							list.css({ height: '', position: 'absolute', visibility: 'hidden' });
							var height = list.height();
							list.css({ height: 0, position: '', visibility: '' });
							list.show();
							list.animate({'height' : height}, E.sections.slideTime, function() {
								list.css('height', '');
							});
							
							if(collection.length > 0) collection.removeClass('closed').addClass('opened');
							$(this).addClass('expanded');
						}
					}
				});
			},
			
			
			/**
			 * Binds the Sortable Event to Scenarios Items
			 * 
			 * When a scenario item is moved, the E.game.scenarios array positions are changed, and the save data is set 
			 * (since the scenarios order is always sent we just need to make sure the E.project.save object is created).
			 */
			scenariosListSort : function() {
				var oldIndex, newIndex;
				
				$('ul.scenarios').sortable({
					start : function(event, ui) {
						oldIndex = $(ui.item).index();
					},
					stop : function(event, ui) {
						newIndex = $(ui.item).index();
						
						// Move on Array
						if(oldIndex != null && newIndex != null) {
							E.game.scenarios.move(oldIndex, newIndex);
							E.tools.setSaveData();
						}
						
						oldIndex = null;
						newIndex = null;
					}
				});
			},
			
			
			/** 
			 * Sets Selected Element Properties
			 * 
			 * If there is a scenario and it must be drawn again, it first draws it.
			 * Then it sets the current element anchors, in case it isn't a scenario.
			 * After it sets the element properties form and enables its tools.
			 */
			setSelected : function(scenario, element, reference, redraw) {

				// Draw Scenario
				if(scenario && !redraw) {
					scenario.draw(redraw, E.sections.canvas.dragAndDrop);
				}

				// Set Anchors
				if(scenario && !(element instanceof E.game.Scenario)) {
					E.anchors.set(element);
				}
				else if(scenario) {
					E.anchors.hide();
				}

				// Set Properties Form
				var isScreen = element && element.parent === 'screen';
				if(scenario || !isScreen) {
					E.sections.properties.setForm(scenario, element, reference);
				}
				else if(isScreen) {
					$('#'+ids.properties+' .'+classes.list).html(E.html.createParagraph(E.strings.labels.screens[element.icon+'Desc']));
				}

				// Enable related Tools
				E.tools.enable($('#'+ids.toolbar+' > ul'), element);
			},
			
			
			/**
			 * Shows an Item by Expanding its Parents
			 *
			 * @param DOM dom - the item dom.
			 * @param bool select - determines if the item must be selected.
			 */
			showItem : function(dom, select) {

				// If not visible expand the collection and scenario elements
				if(!dom.is(':visible')) {
					var collectionArrow = dom.parent('ul.elements').parent('li').children('.arrow');
					var scenarioArrow = collectionArrow.parents('ul.collections').parent('li').children('.arrow');

					if(!scenarioArrow.hasClass('expanded')) {
						scenarioArrow.click();
					}

					if(!collectionArrow.hasClass('expanded')) {
						collectionArrow.click();
					}
				}

				// Select the Item
				if(select) {
					dom.click();
				}
			},
			
			
			/**
			 * Binds the Right Click Event to Explorer Items
			 *
			 * @param string items - selector string.
			 */
			rightClickItems : function(items) {
				$(items).bind("contextmenu", function (event) {
					event.preventDefault(); //Prevent right click menu from being displayed.
					
					E.menus.showRightClickMenu($(this), event);
				});
			}
			
		},
		
		
		/**
		 * Canvas Section functions
		 *
		 * @package E.sections
		 * @subpackage canvas
		 * @author Bruno Sampaio
		 */
		canvas: {
			
			/**
			 * Binds Elements Layer Left or Right Click Event
			 *
			 * On left click the current scenario is selected.
			 * On right click the right click menu is displayed.
			 */
			layerClick : function() {
				E.game.layers.background.on('click', function() {
					if(currentGame.scenario) {
						if(event.which == 1) {
							var id = currentGame.scenario.getId();
							$('#'+ids.explorer+' ul.scenarios > #'+id+' > .'+classes.item).click();
						}
						else if(event.which == 3) {
							E.menus.showRightClickMenu(currentProject.scenario.children('.'+classes.icons.scenario), event);
						}
					} 
				});
			},
			
			/**
			 * Binds Elements Events
			 *
			 * - On mouse over/out, the hand cursor is displayed/hidden respectively.
			 * - On mouse left click the element is selected, and for right click the right click menu is displayed.
			 * - On mouse drag the element and anchors are moved, and its position object is updated. If the element
			 * 	 also has a dom associated it is also moved.
			 *
			 * @param E.game.Element element - the element object.
			 */
			dragAndDrop : function(element) {
				var shape = element.shape, dom = element.dom;
				
				
				/*
				 * Shape Events
				 */
				
				if(!element.isLocked()) {
					shape.setDraggable(true);
				}
				
				// Add cursor styling
				shape.on('mouseover', function(event) {
					if(shape.isDraggable()) {
						E.game.utils.setCursor('grab');
					}
				});
				shape.on('mouseout', function(event) {
					$('body').css('cursor', '');
				});
				
				// Drag Start Event
				shape.on("dragstart click", function(event) {
					if(event.which == 1) {
						
						// Select Element
						if(currentGame.element && shape.getId() != currentGame.element.getId()) {
							E.sections.explorer.showItem(currentProject.scenario.find('#'+shape.getId()), true);
						}
						
					}
					else if(event.which == 3) {
						if(currentProject.scenario.length > 0) {
							E.menus.showRightClickMenu(currentProject.scenario.find('#'+shape.getId()), event);
						}
					}
				});
				
				// Dragging and Drag End Event
				shape.on("dragmove dragend", function(event) {
					
					// Set position values on form fields
					var form = $('#'+ids.properties+' form');
					if(form.attr('action') == shape.getId()) {
						var	position = currentGame.scenario.contentsByID[shape.getId()].position;
						E.sections.properties.switchPositionType(form, 'absolute', shape, position, ids.fields);
					}

					// Set anchors position
					E.anchors.move(shape, true);
				});
				
				
				/*
				 * DOM Events
				 */
				
				if(typeof dom != 'undefined' && dom) {
					var dragging = function(event, ui) {
						var position = ui.position, scale = dom.css('zoom');
						
						position.x = position.left + dom.width()/2;
						position.y = position.top + dom.height()/2;
						if(shape.getTail() == 'top-left' || shape.getTail() == 'top-right') {
							position.y-= shape.getTailHeight()/2;
						}
						else position.y+= shape.getTailHeight()/2;
						
						shape.getDragBoundFunc()(position, null, shape);
						shape.setAbsolutePosition(position.x, position.y);
						
						shape.fire('dragmove', event);
						shape.getLayer().draw();
					};
					
					dom.draggable({ 
						addClasses: false,
						containment: 'parent',
						start : function(event, ui) {
							if(shape.getLayer().getStage().getScale().x < 1) {
								alert(E.strings.alerts.activityDragZoom);
								return false;
							}
							shape.fire('dragstart', event);
						},
						drag : dragging,
						stop : dragging
					}).css('position', '');
					
					// Add cursor styling
					dom.bind('mouseenter', function(event) { shape.fire('mouseover', event); });
					dom.bind('mouseleave', function(event) { shape.fire('mouseout', event); });
					
					// Click
					dom.bind('click contextmenu', function(event) { shape.fire('click', event); });
				}
			}
			
		},
		
		
		/**
		 * Properties Section functions
		 *
		 * @package E.sections
		 * @subpackage properties
		 * @author Bruno Sampaio
		 */
		properties: {
			
			
			/**
			 * Change the Form Fields Status to Enabled/Disabled
			 *
			 * @param bool status - determines if fields must be enabled or disabled.
			 */ 
			changeFormFieldsStatus : function(status) {
				$('#'+ids.properties+' form').find('input, select, button').attr('disabled', status);
			},

			
			/**
			 * Switch the Position from Absolute to Aligned and the Inverse
			 * 
			 * When absolute position is selected, the element position object will contain a point with values to x and y.
			 * When aligned position is selected, the element position object will contain the horizontal and vertical default orientation.
			 * When finished, the data to be saved is also set.
			 *
			 * @param DOM form - the form element.
			 * @param string value - the selected position type.
			 * @param Kinetic.Shape - the shape object.
			 * @param object position - the element position object.
			 * @param object fields - the existing position fields.
			 */
			switchPositionType : function(form, value, shape, position, fields) {
				var parentField = fields.position.general;
				var options = form.find('#'+parentField+' select');
				var selected = form.find('#'+parentField+' > .'+classes.selected);

				if(selected.attr('id') != fields.position[value].general) {
					options.val(value);

					// Switch between Position Type Fields
					selected.removeClass(classes.selected);
					selected = form.find('#'+parentField+' #'+fields.position[value].general).addClass(classes.selected);

					// Absolute
					if(value == 'absolute') {
						delete position.aligned;
						this.setAbsolutePosition(selected, shape, position, fields.position.absolute);
					}
					
					// Aligned
					else {
						delete position.absolute;
						position[value] = {
							horizontal: selected.find('input[name='+fields.position.aligned.horizontal+']:checked').val(),
							vertical: selected.find('input[name='+fields.position.aligned.vertical+']:checked').val()
						};
					}
				}
				else {
					this.setAbsolutePosition(selected, shape, position, fields.position.absolute);
				}

				E.tools.setSaveData(currentGame.scenario.getId(), E.defaults.collections[0], 5, shape.getId());
			},

			
			/**
			 * Sets an Element Absolute Position Object and Form Fields Values
			 *
			 * @param DOM selected - the fields container.
			 * @param Kinetic.Shape shape - the shape object.
			 * @param object position - the element position object.
			 * @param object fields - the absolute position fields. 
			 */
			setAbsolutePosition : function(selected, shape, position, fields) {
				var scale = shape.getLayer().getStage().getScale();
				
				// Update Element Position Object
				var point = shape.getAbsolutePosition();
				point.x = Math.round(point.x / scale.x);
				point.y = Math.round(point.y / scale.y);
				position.absolute = { point: point };
				
				// Update Absolute Position Fields
				selected.find('#'+fields.x).attr('value', point.x);
				selected.find('#'+fields.y).attr('value', point.y);
			},
			
			
			/**
			 * Changes the Field Color from Transparent to Opaque and the Inverse
			 *
			 * @param string field - the input color field id.
			 * @param E.game.Element/Property - the game element or property.
			 * @param string colorArea - the area where to apply the color (font, border, background).
			 * @param bool noColor - determines if color is transparent or opaque.
			 * @param string suffix - the field id suffix, usually empty. 
			 */
			setColorType : function(field, element, colorArea, noColor, suffix) {
				var colorInput = ($('#'+field).length > 0)? $('#'+field) : $('#'+field+'-'+suffix);
				if(colorInput.length > 0) {

					if(noColor) {
						var newColor = 'transparent';
						switch(colorArea) {
							case 'font': element.setFontColor(newColor, suffix); break;
							case 'border': element.setBorderColor(newColor, suffix); break;
							case 'background': element.setBackgroundColor(newColor, suffix); break;
						}
						colorInput.attr('disabled', 'disabled');
					}
					else {
						colorInput.attr('disabled', false);
						colorInput.trigger('input');
					}
				}
			},
			
			
			/**
			 * Adds or Removes a Jump Button
			 *
			 * When a button is created, the jump on field is set with its id.
			 * When a button is removed, the scenario data to be saved is also set.
			 * 
			 * @param E.game.Scenario scenario - the scenario object.
			 * @param string type - the jump/button type.
			 * @param bool add - determines the action to perform (add or remove).
			 */
			addRemoveJumpButton : function(scenario, type, add) {
				if(scenario instanceof E.game.Scenario) {
					var jump = scenario.getJump(type);
					var collection = E.defaults.collections[1];

					if(add && !jump) {
						var button = E.elements.createNew(collection, classes.icons.button, null, type, true);
						scenario.setJumpOn(button.getId(), type);
					}
					else if(!add && jump && ('on' in jump)) {
						var elementDOM = E.scenarios.getCollectionList(scenario.getId(), collection).children('#'+jump.on);
						if(elementDOM.length > 0) {
							E.tools.remove(elementDOM, true);
							E.tools.setSaveData(scenario.getId(), E.defaults.collections[0], 0);
						}
					}
				}
			},
			
			
			/**
			 * Changes an Element Z Index
			 * 
			 * Based on the action selected changes the element z index.
			 * Basically, it changes the scenario contents order:
			 *		- If 'bringToFront' is selected, the element is moved to last position, so it will be the last to be drawn;
			 * 		- If 'sendToBack' is selected, the element is moved to the first position, so it will be the first to be drawn;
			 * 		- If 'bringForward' is selected, the element position is incremented;
			 * 		- If 'sendBackward' is selected, the element position is decremented.
			 *
			 * @param E.game.Scenario scenario - the scenario object.
			 * @param Kinetic.Shape shape - the shape object.
			 * @param string action - the selected field id.
			 */
			changeZIndex : function(scenario, shape, action) {
				var contents = scenario.contents;
				var previousZ = shape.getZIndex();
				var actions = ids.fields.position.z;
				
				var draw = true;
				switch(action) {
					case actions.bringToFront:
						shape.moveToTop();
						break;

					case actions.sendToBack:
						shape.moveToBottom();
						break;

					case actions.bringForward:
						if(previousZ < contents.length-1) {
							shape.moveUp();
						}
						else draw = false;
						break;

					case actions.sendBackward:
						if(previousZ > 1) {
							shape.moveDown();
						}
						else draw = false;
						break;
				}

				if(draw) {
					E.anchors.move(shape);
					E.tools.setSaveData(currentGame.scenario.getId(), E.defaults.collections[0], 5, shape.getId());
					contents.move(previousZ, shape.getZIndex());
					shape.getLayer().draw();
				}
			}
			
		}
		
	};
	
});