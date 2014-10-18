/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	$.extend(E.selectors.ids, {game : 'screen', logo : 'logo' });
	$.extend(E.selectors.classes, {active : 'active', disabled : 'disabled' });
	
	
	/**
	 * General Game functions
	 *
	 * @package E.game
	 * @author Bruno Sampaio
	 */
	E.game = {
		current : {
			scenario : null,
		},
		fadeTime : 400,
		
		
		/**
		 * Initializes the Game
		 *
		 * Creates the stage and layers objects.
		 * Resizes the window.
		 * Unbinds the right click context menu event.
		 * Applies the HTML scenarios events.
		 * Applies the game Modal Windows events.
		 */
		init : function() {
			var scenarioName = E.defaults.screens.game, scenario = $('#'+ids.game+' .'+scenarioName+'.scenario');
			
			// Create the Stage and the Layers
			this.stage = new Kinetic.Stage({ container: scenario[0] });
			this.layers = { 'background': new Kinetic.Layer(), 'elements': new Kinetic.Layer() };
			for(var i in this.layers) {
				this.stage.add(this.layers[i]);
			}
			
			// Resize the Window
			var container = $(this.stage.getContainer());
			this.stage.setSize(container.width(), container.height());
			
			// Remove right click menu from canvas container
			this.getScreen().bind("contextmenu", function (event) { event.preventDefault(); });
			
			// Set Game Events
			this.setScenariosEvents();
			if(typeof this.modal === 'object') {
				this.modal.events();
			}
		},
		
		setSync : function(value) {
			this.sync = value;
		},

		getSync : function() {
			return this.sync;
		},

		/**
		 * Get Game Screen DOM
		 *
		 * The screen DOM contains all the HTML elements that compose the game.
		 * @return DOMElement - the screen DOM.
		 */
		getScreen : function() {
			return $('#'+ids.game);
		},
		
		
		/**
		 * Get the Current Active (or Visible) Scenario
		 *
		 * @param DOMElement container - the scenario container (usually the game screen).
		 * @return DOMElement - the current active scenario DOM.
		 */
		getActiveScenario : function(container) {
			if(!container) container = this.getScreen();
			return container.children('.'+classes.active);
		},
		
		
		/**
		 * Set Current Scenario
		 *
		 * If a 'name' is provided this method will change the active scenario. 
		 * The active scenario is a HTML DOMElement with class 'name'.
		 *
		 * If a 'id' is provided this method will change the current game scenario. 
		 * This is a canvas scenario and for it to be visible the current active scenario must have class .game.
		 *
		 * @param string id - the scenario id.
		 * @param string name - the scenario name.
		 */
		setScenario : function(id, name) {
			
			// Set Current Screen Scenario
			var screens = E.defaults.screens;
			if(name && (name in screens)) {
				var self = this, container = this.getScreen(), activeScenario = this.getActiveScenario(container);
				
				var showActive = function() {
					
					// Show/Hide Players
					if(typeof self.players != 'undefined') {
						if(name == screens.game || name == screens.rankings) {
							self.players.show();
						}
						else self.players.hide();
					}
					
					container.children('.'+name).addClass(classes.active).show();
				};
				
				// Hide Current Scenario and Show New Scenario
				if(activeScenario.length > 0) {
					activeScenario.fadeOut(self.fadeTime, function() {
						activeScenario.removeClass(classes.active);
						showActive();
					});
				}
				else showActive();
			}
			
			// Set Current Game Scenario
			if(id && (id in this.scenarios)) {
				this.scenarios[id].draw();
				this.current.scenario = this.scenarios[id];
			}
			else if(!id && this.current.scenario) {
				this.current.scenario = null;
				self.utils.clearStage();
			}
			
		},
		
		/**
		 * Set HTML Scenarios Events
		 *
		 * This method prevents all forms inside the game screen to be submited.
		 */
		setScenariosEvents : function() {
			$('form').live('submit', function(event) { event.preventDefault(); });
		},
		
		Loading : function(message, count) {
			var box = E.game.getScreen().children('#loading');
			this.bar = box.children('div');
			box.children('p').text(message);
			
			var progress = 0, max = 100, increment = max/count;
			this.bar.progressbar({ value: progress, max: max });
			box.fadeIn();
			
			this.increment = function() {
				progress = (progress + increment > max)? max : progress + increment;
				this.bar.progressbar("option", "value", progress);
			}
			
			this.isFinished = function() {
				var finished = Math.ceil(progress) >= max;
				if(finished) {
					box.fadeOut();
				}
				return finished;
			}
		},
		
		
		/* PROPERTY
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Property : function() {},
		
		
		/* LOGO CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Logo : function(styles) {
			this.__construct();
			
			this.id = '#' + ids.game + ' > ' + '#' + ids.logo;
			this.styles = styles;
			
			this.setCorner(styles.corner);
			$(this.id).bind('dragstart', function(event) { event.preventDefault(); }).show();
		},
		
		
		/* PLAYERS CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Players : function(info, styles) {
			this.__construct();
			
			this.info = info;
			this.styles = styles;
			
			this.elements = E.defaults.types.players;
			this.status = [ 'collaboration', 'jump' ];
		},
		
		
		/* SCORES CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Scores : function() {
			this.__construct();
			
			if(E.defaults.mode == 2) {
				this.team = {};
			}
			
			this.players = {};
			this.helps = {};
		},
		
		
		/* SOUNDS CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Sounds : function() {
			this.__construct();
			
			this.elements = E.defaults.types.sounds;
			for(var i = 0; i < this.elements.length; i++) {
				this[this.elements[i]] = { id: null, file: null };
			}
		},
		
		
		/* ELEMENT CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Element : function() {},
		
		
		/* Scenario
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Scenario : function(id, name, type, contents, rules, jumps, styles) {
			this.__construct(id, name, styles);
			
			this.type = type;
			this.contents = contents;
			this.rules = rules;
			this.jumps = jumps;
			
		},
		
		
		/* TEXT CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Text : function() {},
		
		
		/* HEADING CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Heading : function(id, name, text, styles) {
			this.__construct(id, name, text, styles);
		},
		
		
		/* PARAGRAPH CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Paragraph : function(id, name, text, styles) {
			this.__construct(id, name, text, styles);
		},
		
		
		/* BUTTON CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Button : function(id, name, type, text, styles) {
			this.__construct(id, name, text, styles);
			this.type = type;
		},
		
		
		/* LINE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Line : function(id, name, styles) {
			this.__construct(id, name, styles);
		},
		
		
		/* SQUARE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Square : function(id, name, text, styles) {
			this.__construct(id, name, text, styles);
		},
		
		
		/* CIRCLE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Circle : function(id, name, text, styles) {
			this.__construct(id, name, text, styles);
		},
		
		
		/* BALLOON CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Balloon : function(id, name, text, styles) {
			this.__construct(id, name, text, styles);
		},
		
		
		/* RESOURCE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Resource : function() {},
		
		
		/* AUDIO CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Audio : function(id, name, source, styles) {
			this.__construct(id, name, source, styles);
			this.setData('audio');
		},
		
		
		/* IMAGE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Image : function(id, name, source, styles) {
			this.__construct(id, name, source, styles);
			if(!source) this.setData('image');
		},
		
		
		/* VIDEO CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Video : function(id, name, source, styles) {
			this.__construct(id, name, source, styles);
			this.setData('video');
		},
		
		
		/* FILE CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		PDF : function(id, name, source, styles) {
			this.__construct(id, name, source, styles);
			this.setData('pdf');
		},
		
		
		/* ACTIVITY CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Activity : function() {
			this.solved = false;
		},
		
		
		/* QUESTION CONSTRUCTOR
		 -----------------------------------------------------------------------------------------------------------------------------*/
		Question : function(id, name, source, group, scores, helps, styles) {
			this.__construct(id, name, source, styles);
			
			this.source = source;
			this.group = group;
			this.scores = scores;
			this.helps = helps;
			
			this.question = '';
			this.answers = [];
		}
		
	};
	
	
	/* GAME COLLECTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var collections = E.defaults.collections;
	E.game[collections[0]] = [];
	for(var i = 1; i < collections.length; i++) {
		E.game[collections[i]] = {};
	}
	
	
	/* PROPERTY FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Property.prototype = {

		__construct : function() {
			this.parent = 'property';
		},
		
		getIcon : function() {
			return this.icon;
		},
		
		setIcon : function(value) {
			this.icon = value;
		}
		
	};
	
	
	/* LOGO FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Logo.prototype = {
		
		getDOM : function() {
			return $(this.id);
		},

		getCorner : function() {
			return this.styles.corner;
		},

		setCorner : function(corner) {
			$(this.id).attr('class', corner);
			this.styles.corner = corner;
		}
	};
	$.extend(E.game.Logo.prototype, E.game.Property.prototype);
	
	
	/* ELEMENT FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var setStyleToAll = function(node, apply) {
		if(node.nodeType == 'Group') {
			var i = 0, children = node.getChildren();
			do { apply(children[i]); i++; } while(i < children.length);
		}
		apply(node);
	}
		
	E.game.Element.prototype = {
		
		__construct : function(id, name, styles) {
			this.id = id;
			this.name = name;
			this.styles = styles;
			this.parent = 'element';
		},
		
		getId : function() {
			return this.id;
		},

		getName : function() {
			return this.name;
		},

		setName : function(name) {
			this.name = name;
		},

		getShape : function() {
			return this.getChildrenOfType('.shape');
		},

		getStyles : function() {
			return this.styles;
		},
		
		isLocked : function() {
			return this.locked;
		},
		
		setLocked : function(value) {
			if(typeof this.shape != 'undefined' && this.shape) {
				this.shape.setDraggable(!value);
			}
			if(typeof this.dom != 'undefined' && this.dom) {
				this.dom.draggable(value? 'disable' : 'enable');
			}
			this.locked = value;
		},

		getChildrenOfType : function(type) {
			if(this.shape.nodeType == 'Group') {
				return this.shape.get(type)[0];
			}
			else return this.shape;
		},

		getFontSize : function() {
			return this.styles.font.size;
		},

		setFontSize : function(value) {
			this.getChildrenOfType('.text').setFontSize(value);
			this.styles.font.size = value;
		},

		getFontStyle : function() {
			return this.styles.font.style;
		},

		setFontStyle : function(value) {
			this.getChildrenOfType('.text').setFontStyle(value);
			this.styles.font.style = value;
		},

		getFontColor : function() {
			return this.styles.font.color;
		},

		setFontColor : function(value) {
			this.getChildrenOfType('.text').setFill(value);
			this.styles.font.color = value;
		},

		getWidth : function() {
			if(typeof this.styles.width != 'undefined') {
				return this.styles.width;
			}
			else if(typeof this.styles.length != 'undefined') {
				return this.styles.length;
			}
			else if(typeof this.styles.radius != 'undefined') {
				return this.styles.radius * 2;
			}
		},

		setWidth : function(value) {
			var styles = this.styles;
			setStyleToAll(this.shape, function(shape) {
				if(typeof styles.width != 'undefined') {
					shape.setWidth(value);
					styles.width = value;
				}
				else if(typeof styles.length != 'undefined') {
					shape.setWidth(value);
					styles.length = value;
				}
				else if(typeof styles.radius != 'undefined') {
					shape.setRadius(value / 2);
					styles.radius = value / 2;
				}
			});
		},

		getHeight : function() {
			if(typeof this.styles.height != 'undefined') {
				return this.styles.height;
			}
			else if(typeof this.styles.radius != 'undefined') {
				return this.styles.radius * 2;
			}
			else if(typeof this.shape != 'undefined') {
				return this.shape.getHeight();
			}
		},

		setHeight : function(value) {
			var styles = this.styles;
			setStyleToAll(this.shape, function(shape) {
				if(typeof styles.height != 'undefined') {
					shape.setHeight(value);
					styles.height = value;
				}
				else if(typeof styles.radius != 'undefined') {
					shape.setRadius(value / 2);
					styles.radius = value / 2;
				}
			});
		},

		setSize : function(width, height) {
			this.setWidth(width);
			this.setHeight(height);
		},

		getRotation : function() {
			return Math.abs(this.styles.rotation);
		},

		setRotation : function(value) {
			this.shape.setRotationDeg(-value);
			if(typeof this.dom != 'undefined') {
				this.dom.css({
					'-webkit-transform': 'rotate(-'+value+'deg)',
					'-moz-transform': 'rotate(-'+value+'deg)',
					'-ms-transform': 'rotate(-'+value+'deg)',
					'-o-transform': 'rotate(-'+value+'deg)',
					'transform': 'rotate(-'+value+'deg)'
				});
			}
			this.styles.rotation = -value;
		},

		getBorderThickness : function() {
			return this.styles.border.thickness;
		},

		setBorderThickness : function(value) {
			this.getChildrenOfType('.shape').setStrokeWidth(value);
			this.styles.border.thickness = value;
		},

		getBorderColor : function() {
			return this.styles.border.color;
		},

		setBorderColor : function(value) {
			this.getChildrenOfType('.shape').setStroke(value);
			this.styles.border.color = value;
		},

		getBackgroundColor : function() {
			return this.styles.background.color;
		},

		setBackgroundColor : function(value) {
			this.getChildrenOfType('.shape').setFill(value);
			this.styles.background.color = value;
		},
		
		getIcon : function() {
			return this.icon;
		},
		
		setIcon : function(value) {
			this.icon = value;
		}
	}
	
	
	/* TEXT FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Text.prototype = {
		
		getText : function() {
			return this.text;
		},

		setText : function(value) {
			this.getChildrenOfType('.text').setText(value);
			this.text = value;
		}
		
	};
	$.extend(E.game.Text.prototype, E.game.Element.prototype);
	
	E.game.Text.prototype.super = E.game.Text.prototype.__construct;
	E.game.Text.prototype.__construct = function(id, name, text, styles) {
		this.super(id, name, styles);
		this.text = text;
	}
	
	
	/* RESOURCE FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Resource.prototype = {

		getSource : function() {
			return this.source;
		},
		
		setSource : function(value) {
			this.source = value;
		},
		
		setDOM : function(value) {
			this.dom = value.object;
		}

	};
	$.extend(E.game.Resource.prototype, E.game.Element.prototype);
	
	E.game.Resource.prototype.super = E.game.Resource.prototype.__construct;
	E.game.Resource.prototype.__construct = function(id, name, source, styles) {
		this.super(id, name, styles);
		this.source = source;
		this.image = null;
	};
	
	
	/* ACTIVITIES FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Activity.prototype = {
		
		events : function() {},
		
		isSolved : function() {
			return this.solved;
		},
		
		getSource : function() {
			return this.source;
		},
		
		setSource : function(value) {
			this.source = value;
		}
		
	};
	$.extend(E.game.Activity.prototype, E.game.Element.prototype);
	
	E.game.Activity.prototype.super = E.game.Activity.prototype.__construct;
	E.game.Activity.prototype.__construct = function(id, name, source, styles) {
		this.super(id, name, styles);
		this.source = source;
	}
		
});
