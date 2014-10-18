/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	/**
	 * Game Modal Window functions
	 *
	 * @package E.game
	 * @subpackage modal
	 * @author Bruno Sampaio
	 */
	E.game.modal = {
		
		// Modal Windows Classes
		types : [ 'general', 'waiting', 'warning', 'error' ],
		
		
		/**
		 * Opens the Modal Window on the specified position inside the Game Screen
		 *
		 * @param DOMElement modal - the modal window DOMElement.
		 * @param object position - the modal window position on the screen (if not provided it is the middle).
		 * @return DOMElement - the modal window DOM.
		 */
		open : function(modal, position) {

			if(!modal.is(':visible')) {
				modal.css('visibility', 'hidden').show();
			}

			// Set Window Position
			if(!position) position = {};

			var screen = E.game.getScreen();
			if(typeof position.left === 'undefined') {
				if(typeof position.right === 'undefined') {
					position.left = (screen.outerWidth() - modal.outerWidth())/2;
				}
				else {
					position.left = (screen.outerWidth() - position.right) - modal.outerWidth();
					delete position.right;
				}
			}
			if(typeof position.top === 'undefined') {
				if(typeof position.bottom === 'undefined') {
					position.top = (screen.outerHeight() - modal.outerHeight())/2;
				}
				else {
					position.top = (screen.outerHeight() - position.bottom) - modal.outerHeight();
					delete position.bottom;
				}
			}
			position.visibility = '';

			// Show modal window
			return modal.hide().css(position).fadeIn('fast');
		},
		
		
		/**
		 * Get Modal Window with class 'type'.
		 * 
		 * @param string type - the modal window class.
		 * @return DOMElement - the modal window DOM.
		 */
		get : function(type) {
			var selector = '.modal.window';
			if(type) selector+= '.'+type;
			return E.game.getScreen().children(selector);
		},
		
		
		/**
		 * Set Modal Windows Events
		 * 
		 * Set draggable event and close events.
		 */
		events : function() {
			var self = this, modal = this.get();
			
			// Set Draggable
			E.game.utils.setCursor('grab', modal);
			modal.draggable({ addClasses : false });
			
			// Set Close
			modal.children('.close').click(function(event) {
				self.close($(this).parent());
			});
		},
		
		
		/**
		 * Opens the General Window with the specified content.
		 *
		 * @param string html - the window content.
		 * @param bool noClose - determines if the close button must be visible or not.
		 * @return DOMElement - the modal window DOM.
		 */
		setGeneral : function(html, noClose) {
			return this.open(this.setContent(this.types[0], html, noClose));
		},
		
		
		/**
		 * Opens the General Window with an items selector.
		 *
		 * Creates an items selector DOM for the general window.
		 * 
		 * @param string type - the item title and description prefix to be used on the E.strings.labels array.
		 * @param string name - the content name (usually it is the question text).
		 * @param array list - the list of items to select.
		 * @param int max - the max number of items to select.
		 * @return DOMElement - the modal window DOM.
		 */
		setSelector : function(type, name, list, max) {
			var strings = E.strings.labels;
			if(!name) name = '';
			
			var html = '<div class="select"><h2>'+strings[type+'Title']+'</h2><p>'+strings[type+'Desc']+'</p><p>'+name+'</p><div class="list">';
			for(var i = 0; i < list.length; i++) {
				var item = list[i];
				html+= '<div id="_'+item.id+'" class="item">'+item.content+'</div>';
			}
			html+= '</div><p>'+strings.select.replace('#', max)+'</p></div>';
			
			return this.setGeneral(html, true);
		},
		
		
		/**
		 * Opens the General Window with an Activity Help information.
		 *
		 * @param string type - the item title and description prefix to be used on the E.strings.labels array.
		 * @param string name - the content name (usually it is the question text).
		 * @param array list - the list of items to display.
		 * @param bool hasTimeout - determines if has a timeout field.
		 * @return DOMElement - the modal window DOM.
		 */
		setHelp : function(type, name, list, hasTimeout) {
			var strings = E.strings.labels;
			if(!name) name = '';
			
			// Set List
			var html = '<div class="help"><h2>'+strings[type+'Title']+'</h2><p>'+strings[type+'Desc']+'</p><p>'+name+'</p><div class="list">';
			for(var i = 0; i < list.length; i++) {
				html+= '<div class="item">'+list[i].content+'</div>';
			}
			html+= '</div>';
			
			// Set Timer
			if(hasTimeout) {
				html+= '<p>'+strings.helpTimeout+'</p><div class="timer"><b>'+strings.time+' </b><span class="value"></span></div>'
			}
			html+= '</div>';
			
			return this.setGeneral(html);
		},
		
		
		/**
		 * Opens the Waiting Window with the waiting message with id 'id'.
		 *
		 * @param string id - the waiting message id from E.string.waiting array.
		 * @return DOMElement - the modal window DOM.
		 */
		setWaiting : function(id) {
			if(id in E.strings.waiting) {
				return this.open(this.setContent(this.types[1], E.strings.waiting[id]), { bottom : 40 });
			}
		},
		
		
		/**
		 * Opens the Warning Window with the warning message with id 'id'.
		 *
		 * @param string id - the warning message id from E.string.warnings array.
		 * @return DOMElement - the modal window DOM.
		 */
		setWarning : function(id) {
			if(id in E.strings.warnings) {
				return this.open(this.setContent(this.types[2], E.strings.warnings[id]));
			}
		},
		
		
		/**
		 * Opens the Error Window with the error message with id 'id'.
		 *
		 * @param string id - the error message id from E.string.errors array.
		 * @return DOMElement - the modal window DOM.
		 */
		setError : function(id) {
			if(id in E.strings.errors) {
				return this.open(this.setContent(this.types[3], E.strings.errors[id]));
			}
		},
		
		
		/**
		 * Set the Content for the specified Modal Window.
		 *
		 * @param string type - the modal window class name.
		 * @param string html - the html or text content.
		 * @param bool noClose - determines if the close button must be visible or not.
		 * @return DOMElement - the modal window DOM.
		 */
		setContent : function(type, html, noClose) {
			var modal = this.get(type), close = modal.children('.close');
			
			// Set Window Content
			modal.find('.content').html(html);
			
			// Show/Hide Close Button
			if(noClose) close.hide();
			else close.show();
			
			return modal;
		},
		
		
		/**
		 * Close the Modal Window
		 *
		 * If no Modal Window DOM is provided all windows are closed. After they disapear the background volume is set to normal.
		 * This is necessary because if the window contains a audio, or video element, when they start playing the background volume 
		 * is reduced, so if the window is closed while playing those type of files the background volume must be set to normal again.
		 * 
		 * @param DOMElement modal - the modal window DOM (optional).
		 */
		close : function(modal) {
			var self = this;
			if(!modal) modal = this.get();
			
			if(modal.is(':visible')) {
				modal.fadeOut('fast', function() {
					if(modal.hasClass(self.types[0])) {
						modal.find('.content').html('');
					}
					E.game.sounds.setBackgroundVolume(false);
				});
			}
		}
	};
	
});