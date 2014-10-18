$(document).ready(function() {
	
	/**
	 * Scenarios functions
	 *
	 * @package E.scenarios
	 * @author Bruno Sampaio
	 */
	E.scenarios = {
		
		/**
		 * Creates a new Scenario
		 *
		 * This method creates a new scenario with the given name, rules and styles.
		 * Then the scenario item is created and added to the explorer list.
		 * After that the save data is set, and finally the scenario item is clicked.
		 *
		 * @param string name - scenario name.
		 * @param object rules - scenario rules.
		 * @param object styles - scenario styles.
		 * @return E.game.Scenario scenario - the created scenario object.
		 */
		create : function(type, name, rules, styles) {
			var defaults = E.defaults, collection = defaults.collections[0], scenarios = E.game[collection];
			scenarios.count++;
			
			// Create Scenario Properties
			var contents = [];
			id = defaults.ids[collection] + scenarios.count;
			name = (name? name : defaults.names[collection])  + ' ' + scenarios.count;
			rules = rules? rules : E.utils.cloneObject(defaults.rules[type]);
			styles = styles? styles : E.utils.cloneObject(E.defaults.styles.scenario);
			
			// Create Scenario Object
			var scenario = new E.game.Scenario(id, name, type, contents, rules, {}, styles);
			this.setExtraProperties(scenario, {}, this.createToLoadList(type));
			scenarios.push(scenario);
			
			// Create Scenario HTML
			var item = $(E.html.createScenarioItem(id, name, type, contents));
			
			// Add Scenario to Document
			var list = '#'+ids.explorer+' ul.'+collection;
			$(list).append(item);
			E.sections.explorer.setItemsEvents(list+' #'+id);
			
			// Set Data to Save
			E.tools.setSaveData(id, collection, 0);
			
			item.children('.'+classes.icons.scenario).click();
			return scenario;
		},
		
		
		/**
		 * Creates a New Scenario from a Template
		 * 
		 * This method requests the scenario template with id 'source' and when the data is received,
		 * it creates the scenario element and then its contents based on the provided information.
		 * 
		 * @param int source - scenario template id.
		 */
		createFromTemplate : function(source) {
			var self = this, icons = icons = classes.icons;
			
			E.ajax.request(
				'get', 
				E.system.server + 'scenarios/get/'+source, 
				{}, 
				'json', true, true, 
				function(data) {
					
					if(icons.scenario in data) {
						
						// Create Scenario
						var scenario = data.scenario, width = scenario.width, height = scenario.height;
						scenario = self.create(scenario.type, scenario.name, null, scenario.styles);
						delete data.scenario;
						
						var layer = E.game.layers.elements, resources = E.defaults.resources;
						for(var collection in data) {
							
							for(var item in data[collection]) {
								
								if(item in  classes.icons) {
									var elements = data[collection][item];
									
									for(var i = 0; i < elements.length; i++) {
										var element = elements[i];
										
										// Calculate Element Position
										if('position' in element && 'absolute' in element.position) {
											var point = element.position.absolute.point;
											
											switch(point.x) {
												case 'center':
													point.x = (layer.getWidth()/2);
													break;
													
												default:
													point.x = (layer.getWidth() * point.x)/width;
													break;
											}
											point.x+= layer.getX();
											
											switch(point.y) {
												case 'middle':
													point.y = (layer.getHeight()/2);
													break;
												
												default:
													point.y = (layer.getHeight() * point.y)/height;
													break;
											}
											point.y+= layer.getY();
										}

										// Create Element
										E.elements.create(scenario, collection, icons[item], element.name, element.content, 0, false, element.styles, element.position, true);
									}
								}
							}
						}
					}
				}, 
				function() { delete E.game.current.creating; }
			);
		},
		
		
		/**
		 * Creates a Scenario to Load List
		 *
		 * Basically this list is always composed by a resources list and an activities list.
		 *
		 * @param string type - the scenario type.
		 * @return object list - the to load list.
		 */
		createToLoadList : function(type) {
			var list = {}, collections = E.defaults.collections;
			list[collections[3]] = {};
			if(this.allowsActivities(type)) {
				list[collections[4]] = {};
			}
			return list;
		},
		
		
		/**
		 * Sets Scenario Extra Properties
		 *
		 * Sets the scenario contents by id list, the to load list, and the scenario icon.
		 * 
		 * @param E.game.Scenario scenario - the scenario object.
		 * @param object contentsByID - the scenario contents sorted by element id.
		 * @param object toLoad - the scenario to load list.
		 */
		setExtraProperties : function(scenario, contentsByID, toLoad) {
			scenario.contentsByID = contentsByID;
			scenario.toLoad = toLoad;
			scenario.setIcon(classes.icons.scenario);
		},
		
		
		/**
		 * Adds an Element to a Scenario to Load List 
		 * 
		 * If the element is an image adds its id to the resources list, associated to its source id.
		 * If the element is an activity adds its id and type to the activities list, associated to its source id.
		 * 
		 * @param object list - scenario to load list.
		 * @param string collection - collection of the element to be loaded.
		 * @param E.game.Element element - the element object.
		 */
		addToLoadList : function(list, collection, element) {
			var id = element.getId(), source, collections = E.defaults.collections;
			
			if((collection == collections[3] && element instanceof E.game.Image)) {
				if(source = element.getSource()) {
					if(typeof list[collection][source] == 'undefined') list[collection][source] = [];
					list[collection][source].push(id);
				}
			}
			else if(collection == collections[4]) {
				if(source = element.getSource()) {
					if(typeof list[collection][source] == 'undefined') list[collection][source] = { type: element.isGroup(), list: [] };
					list[collection][source].list.push(id);
				}
			}
		},
		
		
		/**
		 * Gets a Collection Elements List inside a certain Scenario
		 *
		 * @param string scenarioId - scenario id.
		 * @param string collectionName - collection name.
		 * @return DOM
		 */
		getCollectionList : function(scenarioId, collectionName) {
			return $('#'+ids.explorer+' ul.scenarios #'+scenarioId+' ul.collections .'+collectionName+'  ul.elements');
		},
		
		
		/**
		 * Checks if the Specified Scenario allows Activities
		 *
		 * @param string type - the scenario type.
		 * @return bool
		 */
		allowsActivities : function(type) {
			return type == E.defaults.types.scenarios[1];
		},
		
		
		/**
		 * Clones a Scenario Data
		 *
		 * @param E.game.Scenario scenario - the scenario object.
		 * @return object data
		 */
		clone : function(scenario) {
			var clone = E.utils.cloneObject;
			
			var data = {
				type: scenario.type,
				rules: clone(scenario.rules),
				jumps: clone(scenario.jumps),
				toLoad: clone(scenario.toLoad),
				styles: clone(scenario.styles)
			};
			
			return data;
		}
		
	};
	
	
	/**
	 * Elements functions
	 *
	 * @package E.elements
	 * @author Bruno Sampaio
	 */
	E.elements = {
		
		/**
		 * Creates a New Element
		 *
		 * This method creates a new element inside the given scenario and collection,
		 * with the given type, name, content, source, data, styles and position.
		 * The data element contains specific data for a specific element type.
		 * After the element creation, it's also created its item and scenario reference.
		 * And finally, the data to be saved is also set.
		 *
		 * @param E.game.Scenario scenario - the scenario container for the element.
		 * @param string collectionName - the element collection name.
		 * @param string itemType - the element type.
		 * @param string name - the element name.
		 * @param string content - the element text content.
		 * @param int source - the element source id.
		 * @param object data - the element specific data.
		 * @param object styles - the element styles.
		 * @param object position - the element position.
		 * @param bool noselect - determines if the element must be selected after being created.
		 * @return E.game.Element element - the created element.
		 */
		create : function(scenario, collectionName, itemType, name, content, source, data, styles, position, noselect) {
			if(scenario != null || typeof scenario != 'undefined') {
				var layer = E.game.layers.elements, collection = E.game[collectionName], icons = classes.icons, element;
				collection.count++;
				
				var id = E.defaults.ids[collectionName] + collection.count,
					name = (data && typeof data.name != 'undefined')? data.name : ((name? name : E.defaults.names[collectionName]) + ' ' + collection.count);
				
				// Create Item Object
				switch(itemType) {
					case icons.heading:
						element = new E.game.Heading(id, name, content, styles);
						break;

					case icons.paragraph:
						element = new E.game.Paragraph(id, name, content, styles);
						break;

					case icons.button:
						element = new E.game.Button(id, name, data, content, styles);
						break;
						
					case icons.line:
						element = new E.game.Line(id, name, styles);
						break;
						
					case icons.square:
						element = new E.game.Square(id, name, content, styles);
						break;
						
					case icons.circle:
						element = new E.game.Circle(id, name, content, styles);
						break;
					
					case icons.balloon:
						element = new E.game.Balloon(id, name, content, styles);
						break;
						
					case icons.audio:
						element = new E.game.Audio(id, name, source, styles);
						break;
							
					case icons.image:
						element = new E.game.Image(id, name, source, styles);
						if(data) element.setData(data);
						break;
						
					case icons.video:
						element = new E.game.Video(id, name, source, styles);
						break;
							
					case icons.pdf:
						element = new E.game.PDF(id, name, source, styles);
						break;
						
					case icons.question: case icons.group:
						var category = (itemType == classes.icons.group);
						var scores = E.utils.cloneObject(E.defaults.scores);
						var helps = this.createActivityHelps(itemType, data);
						
						element = new E.game.Question(id, name, source, category, scores, helps, styles);
						if(data) element.setData(data);
						break;
				}
				
				collection[id] = element;
				element.createShape();
				this.setExtraProperties(element, false, itemType);
				E.scenarios.addToLoadList(scenario.toLoad, collectionName, element);

				// Create Element Reference
				var reference = this.createReference(id, collectionName, position);
				scenario.contents.push(reference);
				scenario.contentsByID[id] = scenario.contents[scenario.contents.length - 1];
				
				// Create Element Item and Add It to Document
				var item = $(E.html.createElementItem(id, name, itemType));
				E.scenarios.getCollectionList(scenario.getId(), collectionName).append(item);
				
				// Add Item Events
				E.sections.explorer.selectItems(item);
				E.sections.explorer.rightClickItems(item);
				
				// Calculate element correct position and add it to the Canvas
				if(currentGame.scenario && scenario.getId() == currentGame.scenario.getId()) {
					layer.add(element.shape);
					E.game.utils.setShapePosition(layer, element, reference.position);
					layer.draw();
				}
				
				// Set data to be saved when project save is triggered
				E.tools.setSaveData(id, collectionName, 0);
				E.tools.setSaveData(scenario.getId(), E.defaults.collections[0], 5, id);
				
				// Select Item
				if(!noselect) E.sections.explorer.showItem(item, true);
				
				return element;
			}
		},
		
		
		/**
		 * Creates a New Element with Default Properties
		 * 
		 * This methos invokes the E.elements.create function, 
		 * passing to it the default properties for this element type,
		 * the current selected scenario, and a random position.
		 * 
		 * @param string collectionName - the element collection name.
		 * @param string itemType - the element type.
		 * @param int source - the element source id.
		 * @param object data - the element specific data.
		 * @param bool noselect - determines if the element must be selected after being created.
		 * @return E.game.Element - the created element.
		 */
		createNew : function(collectionName, itemType, source, data, noselect) {
			
			// Set Item Properties
			var layer = E.game.layers.elements,
				content = (itemType in E.defaults.contents)? E.defaults.contents[itemType] : '',
				styles = E.utils.cloneObject(E.defaults.styles[itemType]),
				position = {
					absolute: {
						point: {
							x: Math.ceil(Math.random()*layer.getWidth()),
							y: Math.ceil(Math.random()*layer.getHeight())
						}
					}
				};
				
			if(itemType == classes.icons.button) content = content[data];
				
			return this.create(currentGame.scenario, collectionName, itemType, false, content, source, data, styles, position, noselect);
		},
		
		
		/**
		 * Creates or Modifies an Element with the given Source Id and Loaded Data
		 *
		 * This method receives data loaded from the server and uses it to create or modify a resource or activity element. 
		 * If an id is provided, it's a modification, otherwise, it's a creation.
		 * 
		 * @param string id - the element id (null for creation).
		 * @param string collectionName - the element collection name.
		 * @param string itemType - the element type.
		 * @param int source - the element source id.
		 * @param object data - the element specific data.
		 */
		createOrModifyFromChosen : function(id, collectionName, itemType, source, data) {
			if(typeof id == 'undefined' || !id) {
				this.createNew(collectionName, itemType, source, data);
			}
			else {
				var scenario = currentGame.scenario, contents = scenario.contentsByID;
				if(typeof contents[id] != 'undefined') {
					var element = E.game[collectionName][id], 
						reference = contents[id],
						dom = E.scenarios.getCollectionList(scenario.getId(), collectionName).children('#'+id);
					
					if(typeof data.name != 'undefined') {
						element.setName(data.name);
						dom.children('.name').text(data.name);
					}
					element.setSource(source);
					element.setData(data);
					
					if(element instanceof E.game.Question) {
						dom.removeClass(element.getIcon()).addClass(itemType);
						element.isGroup(itemType == classes.icons.group);
						element.setHelps(this.createActivityHelps(itemType, data));
						element.setIcon(itemType);
						E.sections.explorer.setSelected(scenario, element, reference);
					}
					else {
						element.shape.getLayer().draw();
						E.sections.properties.setForm(scenario, element, reference);
					}
					
					E.scenarios.addToLoadList(scenario.toLoad, collectionName, element);
					E.tools.setSaveData(id, collectionName, 3);
				}
			}
		},
		
		
		/**
		 * Creates an Element Reference Object
		 *
		 * This method creates the reference object to be passed to the scenario object.
		 * A reference is composed by the element id, collection, and position.
		 *
		 * @param string id - the element id.
		 * @param string collectionName - the element collection name.
		 * @param object position - the element position object.
		 * @return object - the reference object.
		 */
		createReference : function(id, collectionName, position) {
			return { id: id, collection: collectionName, position: position };
		},
		
		
		/**
		 * Creates Activity Helps Object
		 *
		 * Load the helps object for the specified activity type from E.defaults.helps 
		 * and loads into it the list of selectable and selected items.
		 *
		 * @param string itemType - the element type.
		 * @param object data - the element specific data.
		 */
		createActivityHelps : function(itemType, data) {
			var helps = E.defaults.helps[itemType], isGroup = (itemType == classes.icons.group);
			if(!isGroup) {
				if(data && typeof data.type != 'undefined' && typeof helps[data.type] != 'undefined') {
					helps = helps[data.type];
				}
				else helps = {};
			}
			helps = E.utils.cloneObject(helps);
			
			// Set Selected Values
			this.setActivityExtraProperties(data, helps);
			
			return helps;
		},
		
		
		/**
		 * Determines if an Element is Loadable
		 *
		 * A loadable element is an element that must be loaded from the server before being displayed.
		 * Currently, the loadable elementes available are the images, questions, and questions groups.
		 *
		 * @param string itemType - the element type.
		 * @return bool
		 */
		isLoadable : function(itemType) {
			return itemType == classes.icons.image || itemType == classes.icons.question || itemType == classes.icons.group;
		},
		
		
		/**
		 * Create Element to Load Data
		 *
		 * In case a sole element needs to be loaded from the server,
		 * this method creates a to load list with the elemente source and type.
		 *
		 * @param string collectionName - the element collection name.
		 * @param string itemType - the element type.
		 * @param int source - the element source id.
		 * @return object toLoad - data to send to server.
		 */
		createToLoadData : function(collectionName, itemType, source) {
			var collections = E.defaults.collections;
			
			toLoad = {};
			if(source) {
				toLoad[collectionName] = {};
				if(collectionName == collections[4]) {
					toLoad[collectionName][source] = { type: (itemType == classes.icons.group) };
				}
				else toLoad[collectionName][source] = true;
			}
			
			return toLoad;
		},
		
		
		/**
		 * Sets Element Extra Properties
		 *
		 * Sets the element locked status and icon.
		 *
		 * @param E.game.Element element - the element object.
		 * @param bool locked - determines if element is locked.
		 * @param string icon - the element icon.
		 */
		setExtraProperties : function(element, locked, icon) {
			element.setIcon(icon);
			if(typeof element.shape != 'undefined') {
				E.sections.canvas.dragAndDrop(element);
			}
			element.setLocked(locked);
		},
		
		
		/**
		 * Sets Activity Element Extra Properties
		 *
		 * Sets the helps selectable list.
		 * 
		 * @param object data - the element data.
		 * @param object helps - the element helps list.
		 */
		setActivityExtraProperties : function(data, helps) {
			if(data && typeof data.selectable != 'undefined' && typeof helps != 'undefined') {
				for(var i in helps) {
					if(i in data.selectable) {
						var selectable = data.selectable[i];

						// Set Help List of Selectable Elements
						if(typeof selectable == 'object') {
							if(!$.isEmptyObject(selectable)) {
								helps[i].all = selectable;
							}
							else helps[i].all = 0;
							
							// Set Help List of Selected Elements
							var all = helps[i].all;
							if(!all && helps[i].use) {
								helps[i].use = 0;
							}
							else if(all && helps[i].use && !helps[i].selected) {
								var selectAll = i == 'hints';
								
								helps[i].selected = {};
								for(var j in all) {
									helps[i].selected[j] = 1;
									if(!selectAll) break;
								}
							}
						}
					}
				}
				
				delete data.selectable;
			}
		},
		
		
		/**
		 * Gets the Scenario Item for the given Element Item.
		 *
		 * @param DOM dom - the element item dom.
		 * @return DOM
		 */
		getScenarioItem : function(dom) {
			return dom.parent('ul.elements').parent('li').parent('ul.collections').parent('li');
		},
		
		
		/**
		 * Clones an Element Data
		 *
		 * @param E.game.Element element - the element object.
		 * @param object reference - the element reference from a scenario.
		 * @return object data
		 */
		clone : function(element, reference) {
			var data = {
				reference: E.utils.cloneObject(reference),
				styles: E.utils.cloneObject(element.styles),
				icon: element.icon
			};
			
			if(typeof element.text != 'undefined') {
				data.text = element.text;
			}
			
			if(typeof element.source != 'undefined') {
				data.source = element.source;
			}
			
			return data;
		}
		
	};
	
	
	/* ANCHORS ACTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.anchors = {
		
		/**
		 * Creates an Anchor
		 *
		 * This method creates an anchor and binds the mouse over/out, click, and drag events to it.
		 *
		 * @param Kinetic.Layer - the layer object.
		 * @param Kinetic.Group - the anchors group.
		 * @param string name - the anchor name.
		 * @return E.game.Circle anchor - the anchor object.
		 */
		create : function(layer, group, name) {
			var anchor = new Kinetic.Circle({ 
				name: name, 
				stroke: "#666", 
				fill: "#ddd", 
				strokeWidth: 2, 
				radius: 8, 
				draggable: true,
				dragBoundFunc: function(position) {
					var anchors = E.anchors.get(group),
						scale = this.getLayer().getStage().getScale(),
						elementStyles = currentGame.element.styles,
						noHeight = (typeof elementStyles.height === 'undefined' && typeof elementStyles.radius === 'undefined');
					
					switch(this.getName()) {
						case 'topLeft':
							position.x = (position.x > anchors.topRight.getX() * scale.x)? anchors.topRight.getX() * scale.x : position.x;
							if(noHeight) position.y = this.getY() * scale.y;
							else position.y = (position.y > anchors.bottomLeft.getY() * scale.y)? anchors.bottomLeft.getY() * scale.y : position.y;
							break;
						
						case 'topRight':
							position.x = (position.x < anchors.topLeft.getX() * scale.x)? anchors.topLeft.getX() * scale.x : position.x;
							if(noHeight) position.y = this.getY() * scale.y;
							else position.y = (position.y > anchors.bottomRight.getY() * scale.y)? anchors.bottomRight.getY() * scale.y : position.y;
							break;
							
						case 'bottomLeft':
							position.x = (position.x > anchors.bottomRight.getX() * scale.x)? anchors.bottomRight.getX() * scale.x : position.x;
							if(noHeight) position.y = this.getY() * scale.y;
							else position.y = (position.y < anchors.topLeft.getY() * scale.y)? anchors.topLeft.getY() * scale.y : position.y;
							break;
							
						case 'bottomRight':
							position.x = (position.x < anchors.bottomLeft.getX() * scale.x)? anchors.bottomLeft.getX() * scale.x : position.x;
							if(noHeight) position.y = this.getY() * scale.y;
							else position.y = (position.y < anchors.topRight.getY() * scale.y)? anchors.topRight.getY() * scale.y : position.y;
							break;
					}
					
					return position;
				}
			});
			
			anchor.on("dragmove", function() {
				this.moveToTop();
				E.anchors.update(currentGame.element, this);
				layer.draw();
    		});
			
	        // Add hover styling
	        anchor.on("mouseover", function() {
				var elementStyles = currentGame.element.styles;
				if(typeof elementStyles.height === 'undefined' && typeof elementStyles.radius === 'undefined') {
					switch(this.getName()) {
						case 'topLeft': case 'bottomLeft':
							$('body').css('cursor', 'w-resize');
							break;

						case 'topRight': case 'bottomRight':
							$('body').css('cursor', 'e-resize');
							break;
					}
				}
				else {
					switch(this.getName()) {
						case 'topLeft': case 'bottomRight':
							$('body').css('cursor', 'nwse-resize');
							break;

						case 'topRight': case 'bottomLeft':
							$('body').css('cursor', 'nesw-resize');
							break;
					}
				}
				
				this.setStrokeWidth(4);
				layer.draw();
	        });
			
	        anchor.on("mouseout", function() {
				$('body').css('cursor', '');
				this.setStrokeWidth(2);
				layer.draw();
	        });

	        group.add(anchor);
			return anchor;
		},
		
		
		/**
		 * Sets the Anchors for the given Element
		 * 
		 * @param E.game.Element element - the element object.
		 */
		set : function(element) {
			var anchors = this.get();
			var shape = element.shape;
			var layer = shape.getLayer();
			
			if(!(element instanceof E.game.Scenario) && !element.isLocked()) {
				var position = shape.getPosition();
				
				// Create or Display Anchors
				if(!anchors) {
					anchors = { group : new Kinetic.Group({ id: 'anchors' }) };
					layer.add(anchors.group);
					
					anchors.topLeft = this.create(layer, anchors.group, 'topLeft');
					anchors.topRight = this.create(layer, anchors.group, 'topRight');
					anchors.bottomLeft = this.create(layer, anchors.group, 'bottomLeft');
					anchors.bottomRight = this.create(layer, anchors.group, 'bottomRight');
				}
				else {
					anchors.group.setVisible(true);
				}
				
				// Display only the necessary anchors
				if(element instanceof E.game.Line) {
					anchors.bottomLeft.setVisible(false);
					anchors.bottomRight.setVisible(false);
				}
				else {
					anchors.bottomLeft.setVisible(true);
					anchors.bottomRight.setVisible(true);
				}
				
				this.move(shape);
			}
			else if(anchors) {
				anchors.group.hide();
			}
			layer.draw();
		},
		
		
		/**
		 * Gets the Anchors Group and each Individual Anchor
		 *
		 * @return object
		 */
		get : function() {
			var group = E.game.layers.elements.get('#anchors');
			if(group.length > 0) {
				group = group[0];
				
				return { 
					group: group,
					topLeft: group.get(".topLeft")[0],
					topRight: group.get(".topRight")[0], 
					bottomLeft: group.get(".bottomLeft")[0],
					bottomRight: group.get(".bottomRight")[0]
				};
			}
			else return null;
		},
		
		
		/**
		 * Moves the Anchors based on the given Shape Position
		 * 
		 * @param Kinetic.Shape shape - the shape object.
		 * @param bool moving - determines if anchors are being moved.
		 */
		move : function(shape, moving) {
			var anchors = this.get();
			if(anchors) {
				var group = anchors.group, position = shape.getPosition();
				
				group.setOffset(position.x, position.y);
				group.setPosition(position.x, position.y);
				
				var halfWidth = shape.getWidth()/2, halfHeight = shape.getHeight()/2,
					top = position.y - halfHeight,
					left = position.x - halfWidth,
					right = position.x + halfWidth,
					bottom = position.y + halfHeight;
					
				anchors.topLeft.setPosition(left, top);
				anchors.topRight.setPosition(right, top);
				anchors.bottomLeft.setPosition(left, bottom);
				anchors.bottomRight.setPosition(right, bottom);

				if(!moving) {
					group.moveToTop();
				}
			}
		},
		
		
		/**
		 * Updates an Element Size and Position based on the Anchors Position
		 * 
		 * @param E.game.Element element - the element object.
		 * @param Kinetic.Circle - the anchor object.
		 */
		update : function(element, activeAnchor) {
			var shape = element.shape;
			var anchors = this.get();
			if(anchors) {

				// Update Anchor Positions
				switch (activeAnchor.getName()) {
					case "topLeft":
						anchors.topRight.setY(activeAnchor.getY());
						anchors.bottomLeft.setX(activeAnchor.getX());
			            break;

					case "topRight":
						anchors.topLeft.setY(activeAnchor.getY());
						anchors.bottomRight.setX(activeAnchor.getX());
						break;

					case "bottomRight":
						anchors.bottomLeft.setY(activeAnchor.getY());
						anchors.topRight.setX(activeAnchor.getX());
						break;

					case "bottomLeft":
						anchors.bottomRight.setY(activeAnchor.getY());
						anchors.topLeft.setX(activeAnchor.getX());
			            break;
				}

				// Calculate new Positions and Sizes
				var scale = shape.getLayer().getStage().getScale();
				var width = anchors.topRight.getX() - anchors.topLeft.getX();
				var height = anchors.bottomLeft.getY() - anchors.topLeft.getY();
				var x = anchors.topLeft.getX() + width/2;
				var y = anchors.topLeft.getY() + height/2;

				// Set Shape new Position
				if(!(element instanceof E.game.Circle)) {
					shape.setOffset(width/2, height/2);
				}
				shape.setPosition(x, y);

				// Set Shape new Size
				var form = $('#'+ids.properties+' form'), fields = ids.fields.dimensions, isCurrent = form.attr('action') == element.getId();
				if(element instanceof E.game.Line) {
					element.setWidth(width);
					shape.setPoints([0, 0, element.getWidth(), 0]);
					if(isCurrent) form.find('#'+fields.length).val(element.getWidth());
				}
				else if(element instanceof E.game.Circle) {
					element.setRadius(Math.min(width, height)/2);
					if(isCurrent) form.find('#'+fields.radius).val(element.getRadius());
				}
				else {
					element.setSize(width, height);
					if(isCurrent) {
						form.find('#'+fields.width).val(width);
						form.find('#'+fields.height).val(height);
					}
				}
				
				// Set position on form fields and set data to save
				if(isCurrent) {
					var	reference = currentGame.scenario.contentsByID[element.getId()];
					E.sections.properties.switchPositionType(form, 'absolute', shape, reference.position, ids.fields);
					E.tools.setSaveData(element.getId(), reference.collection, 6);
				}
			}
		},
		
		
		/**
		 * Hides the Anchors
		 */
		hide : function() {
			var anchors = this.get();
			if(anchors) {
				anchors.group.setVisible(false);
				anchors.group.getLayer().draw();
			}
		}
		
	};
	
});