/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	/**
	 * Time functions
	 *
	 * @package E.game
	 * @author Bruno Sampaio
	 */
	$.extend(E.game, {
		
		/**
		 * TIME MANAGER CONSTRUCTOR
		 */
		TimeManager : function() {
			this.intervals = {};
			this.timeouts = {};
		},
		
		timer : {
			
			// Timer Types
			types : [ 'ScenarioTimeout', 'HelpTimeout' ],
			
			
			/**
			 * Gets Timer of type specified
			 *
			 * @param string type - type from the types array.
			 * @return DOMElement - the timer DOM.
			 */
			get : function(type) {
				switch(true) {

					case type.startsWith(this.types[0]): 
						return E.game.getScreen().children('.timer');

					case type.startsWith(this.types[1]): 
						var modal = E.game.modal;
						return modal.get(modal.types[0]).find('.timer');
				}
			},
			
			
			/**
			 * Creates Timer with the specified ID starting on the specified time.
			 *
			 * @param string id - the timer id.
			 * @param int time - the time to start counting.
			 * @param function callback - the callback function to be executed when the timeout occurs.
			 */
			set : function(id, time, callback) {
				var self = this, 
					game = E.game, 
					type = id,
					dom = this.get(type);

				// Set Timeout
				game.timeManager.setTimeout(type, function() {
					dom.fadeOut();

					// Clear Interval and Timeout
					game.timeManager.clearTimeout(type);
					game.timeManager.clearInterval(type);

					callback();

				}, time * 1000);

				// Set Interval
				this.updateTime(dom, time);
				game.timeManager.setInterval(type, function() {
					self.updateTime(dom);
				}, 1000);

				// Show the Timer
				dom.fadeIn();
			},
			
			
			/**
			 * Set the Timer DOM Position
			 *
			 * @param DOMElement dom - the timer DOM.
			 */
			setPosition : function(dom) {
				var game = E.game;
				if(game.getScreen()[0] == dom.parent()[0]) {
					var position = (dom.outerWidth()/2) + ((game.stage.getWidth() - game.layers.elements.getWidth())/2);
					if(game.players.isLeft()) {
						position-= game.players.dom.outerWidth()/2;
					}
					else {
						position = -position;
					}
					dom.css('margin-left', position);
				}
			},
			
			
			/**
			 * Updates the Time
			 *
			 * @param DOMElement dom - the timer DOM element.
			 * @param int time - the time to start (optional).
			 */
			updateTime : function(dom, time) {
				var valueDOM = dom.children('.value');

				if(!time) {
					time = valueDOM.data('seconds');
					time--;
				}

				if(time >= 0) {
					valueDOM.data('seconds', time);
					valueDOM.text(E.game.utils.secondsToMinutes(time));
					this.setPosition(dom);
				}
			},
			
			
			/**
			 * Hides the Timer
			 *
			 * If no dom specified it hides all timers on the game screen.
			 * 
			 * @param DOMELement dom - container DOM (optional).
			 */
			hide : function(dom) {
				if(!dom) dom = E.game.getScreen();
				dom.find('.timer').hide();
			}
		}
		
	});
	
	
	/* TIME MANAGER FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.TimeManager.prototype = {
		
		setInterval : function(id, callback, time) {
			if(id in this.intervals) {
				this.clearInterval(id);
			}
			
			var self = this;
			this.intervals[id] = setInterval(callback, time);
		},
		
		setTimeout : function(id, callback, time) {
			if(id in this.timeouts) {
				this.clearTimeout(id);
			}
			
			var self = this;
			this.timeouts[id] = setTimeout(function() { 
				callback();
				delete self.timeouts[id];
			}, time);
		},
		
		clearInterval : function(id) {
			if(id in this.intervals) {
				clearInterval(this.intervals[id]);
				delete this.intervals[id];
				return true;
			}
			return false;
		},
		
		clearTimeout : function(id) {
			if(id in this.timeouts) {
				clearTimeout(this.timeouts[id]);
				delete this.timeouts[id];
				return true;
			}
			return false;
		},
		
		clearAll : function() {
			for(var i in this.intervals) {
				this.clearInterval(i);
			}
			
			for(var i in this.timeouts) {
				this.clearTimeout(i);
			}
			
			E.game.timer.hide();
		}
	};
	
});