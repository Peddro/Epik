/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	/**
	 * Game Utilities functions
	 *
	 * @package E.game
	 * @subpackage utils
	 * @author Bruno Sampaio
	 */
	E.game.utils = {
		
		/**
		 * Loads Audio, Image, Video, or PDF Files
		 * 
		 * @param string message - the message for the loading bar.
		 * @param object list - the resources list to load.
		 * @param function callback - the function to invoke when loading is finished.
		 */
		loadResources : function(message, list, callback) {
			var length = Object.keys(list).length;
			
			if(length > 0) {
				var progressbar = new E.game.Loading(message, length);

				// Function to execute after resource is loaded
				var onload = function(event) {
					if(this instanceof Audio) {
						this.removeEventListener('canplay', arguments.callee, false);
						this.removeEventListener('canplaythrough', arguments.callee, false);
					}
					progressbar.increment();

					if(progressbar.isFinished()) {
						callback();
					}
				};

				// Iterate over the resources list
				for(var i in list) {
					var resource = list[i];

					// Create the object type
					switch(resource.type) {
						case 'audio':
							resource.object = new Audio();
							resource.object.addEventListener('canplay', onload, false);
							resource.object.addEventListener('canplaythrough', onload, false);
							resource.object.onerror = onload;
							resource.object.controls = true;
							resource.object.src = resource.url;
							break;

						case 'image':
							resource.object = new Image();
							resource.object.onload = onload;
							resource.object.onerror = onload;
							resource.object.src = resource.url;
							break;

						case 'video':
							if(resource.external) resource.object = $('<iframe src="'+resource.url+'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>')[0];
							else resource.object = $('<video controls="controls"><source src="'+resource.url+'"></video>')[0];
							onload();
							break;

						case 'pdf':
							resource.object = $('<embed src="'+resource.url+'" />')[0];
							onload();
							break;
					}
				}
			}
			else callback();
		},
		
		
		/**
		 * Set Shape Position
		 * 
		 * If position is aligned it calculates the x and y values.
		 * For all shapes types, except circles, it sets the shape offset to the center point.
		 * In the end, it checks if the shape is inside the layer bounds, and if not changes its position.
		 *
		 * @param Kinetic.Layer layer - the shape layer.
		 * @param E.game.Element - the element object.
		 * @param object position - the position object.
		 * @return object point
		 */
		setShapePosition : function(layer, element, position) {
			var width = element.getWidth(), height = element.getHeight(), border = element.getBorderThickness(), scale = layer.getStage().getScale()
				shape = element.shape,
				bounds = shape.getDragBoundFunc(),
				point = {};
			
			// If position is Absolute
			if(typeof position.absolute === 'object') {
				point = $.extend(true, {}, position.absolute.point);
			}
			
			// If position is Aligned
			else if(typeof position.aligned === 'object') {
				switch(position.aligned.horizontal) {
					case 'left':
						point.x = layer.getX() + border/2 + width/2;
						break;
						
					case 'center':
						point.x = layer.getX() + layer.getWidth()/2;
						break;
						
					case 'right':
						point.x = layer.getX() + layer.getWidth() - width/2 - border/2;
						break;
				}
				
				switch(position.aligned.vertical) {
					case 'top':
						point.y = layer.getY() + border/2 + height/2;
						break;
						
					case 'middle':
						point.y = layer.getY() + layer.getHeight()/2;
						break;
						
					case 'bottom':
						point.y = layer.getY() + layer.getHeight() - height/2 - border/2;
						break;
				}
			}
			
			point.x*= scale.x;
			point.y*= scale.y;
			
			// Set Element Offset
			if(!(element instanceof E.game.Circle)) {
				shape.setOffset(shape.getWidth()/2, shape.getHeight()/2);
			}
			
			// Set Element Position
			if(element instanceof E.game.Line) {
				shape.setPoints([0, 0, shape.getWidth(), 0]);
			}
			
			bounds(point, null, shape);
			shape.setAbsolutePosition(point.x, point.y);
			
			return point;
		},
		
		
		/**
		 * Drag Bounds
		 *
		 * Verifies if shape is inside its layer bounds and calculates its new position if not.
		 *
		 * @param object position - the shape current position.
		 * @param object event - the event object.
		 * @param Kinetic.Shape shape - the shape object.
		 * @return object position
		 */
		dragBounds : function(position, event, shape) {
			shape = shape? shape : this;
			
			// Get Layer Values
			var layer = shape.getLayer(), 
				scale = layer.getStage().getScale(),
				layerBoundX = layer.getX() * scale.x, 
				layerBoundY = layer.getY() * scale.y, 
				layerBoundW = (layer.getX() + layer.getWidth()) * scale.x, 
				layerBoundH = (layer.getY() + layer.getHeight()) * scale.y;
			
			// Get Logo Value
			var logo = E.game.logo, 
				corner = logo.getCorner(), 
				logoDOM = logo.getDOM(),
				logoPos = {},
				logoW = logoDOM.outerWidth() * scale.x,
				logoH = logoDOM.outerHeight() * scale.y;
				
			// Get Shape Values
			var halfWidth = shape.getWidth()/2, 
				halfHeight = shape.getHeight()/2;
			
			if(shape.nodeType != 'Group') {
				halfWidth+= shape.getStrokeWidth()/2;
				halfHeight+= shape.getStrokeWidth()/2;
			}
			halfWidth*= scale.x;
			halfHeight*= scale.y;
			
			// Set Horizontal Bounds
			if((position.x - halfWidth) < layerBoundX) position.x = layerBoundX + halfWidth;
			else if((position.x + halfWidth) > layerBoundW) position.x = layerBoundW - halfWidth;
			
			// Set Vertical Bounds
			if((position.y - halfHeight) < layerBoundY) position.y = layerBoundY + halfHeight;
			else if((position.y + halfHeight) > layerBoundH) position.y = layerBoundH - halfHeight;
			
			switch(corner) {
				case 'top-left':
					
					// Get shape top left corner position
					position.x-= halfWidth;
					position.y-= halfHeight;
					
					// Get logo bottom right corner position
					logoPos.right = logoW;
					logoPos.bottom = logoH;
					
					// Set shape position
					if((logoPos.right - position.x) > (logoPos.bottom - position.y)) {
						if(position.y < logoPos.bottom) {
							position.y = logoPos.bottom;
						}
					}
					else {
						if(position.x < logoPos.right) {
							position.x = logoPos.right;
						}
					}
					
					position.x+= halfWidth;
					position.y+= halfHeight;
					break;
					
				case 'top-right':
					
					// Get shape top right corner position
					position.x+= halfWidth;
					position.y-= halfHeight;
					
					// Get logo bottom left corner position
					logoPos.left = layerBoundW - logoW;
					logoPos.bottom = logoH;
					
					// Set shape position
					if((position.x - logoPos.left) > (logoPos.bottom - position.y)) {
						if(position.y < logoPos.bottom) {
							position.y = logoPos.bottom;
						}
					}
					else {
						if(position.x > logoPos.left) {
							position.x = logoPos.left;
						}
					}
					
					position.x-= halfWidth;
					position.y+= halfHeight;
					break;
					
				case 'bottom-left':

					// Get shape bottom left corner position
					position.x-= halfWidth;
					position.y+= halfHeight;

					// Get logo top right corner position
					logoPos.top = layerBoundH - logoH;
					logoPos.right = logoW;

					// Set shape position
					if((logoPos.right - position.x) > (position.y - logoPos.top)) {
						if(position.y > logoPos.top) {
							position.y = logoPos.top;
						}
					}
					else {
						if(position.x < logoPos.right) {
							position.x = logoPos.right;
						}
					}

					position.x+= halfWidth;
					position.y-= halfHeight;
					break;
					
				case 'bottom-right':

					// Get shape bottom right corner position
					position.x+= halfWidth;
					position.y+= halfHeight;
					
					// Get logo top left corner position
					logoPos.top = layerBoundH - logoH;
					logoPos.left = layerBoundW - logoW;

					// Set shape position
					if((position.x - logoPos.left) > (position.y - logoPos.top)) {
						if(position.y > logoPos.top) {
							position.y = logoPos.top;
						}
					}
					else {
						if(position.x > logoPos.left) {
							position.x = logoPos.left;
						}
					}

					position.x-= halfWidth;
					position.y-= halfHeight;
					break;
			}
			
			return position;
		},
		
		
		/**
		 * Create Rectangular Text Box
		 * 
		 * @param string id - the group identifier.
		 * @param string content - the text content.
		 * @param string font - the font familly name.
		 * @param int padding - the text field padding.
		 * @param int lineHeight - the text line height.
		 * @param string align - the text alignement.
		 * @param object styles - the styles object.
		 * @return Kinetic.Group box
		 */
		createTextBox : function(id, content, font, padding, lineHeight, align, styles) {
			var box = new Kinetic.Group({ 
				id: id,
				width: styles.width,
				height: styles.height,
				rotationDeg: styles.rotation,
				dragBoundFunc: E.game.utils.dragBounds
			});
			
			var shape = new Kinetic.Rect({
				name: 'shape',
				width: styles.width,
				height: styles.height,
				fill: styles.background.color,
				stroke: styles.border.color,
				strokeWidth: styles.border.thickness
			});
			
			var text = new Kinetic.Text({
				name: 'text',
				fontFamily: font,
				fontSize: styles.font.size,
				fontStyle: styles.font.style,
				fill: styles.font.color,
				width: styles.width,
				height: styles.height,
				text: content,
				padding: padding,
				lineHeight: lineHeight,
				align: align
			});
			
			box.add(shape);
			box.add(text);
			
			return box;
		},
		
		
		/**
		 * Create Balloon
		 * 
		 * @param object styles - the balloon styles.
		 * @param function beforeDraw - before draw callback.
		 * @return Kinetic.Shape balloon
		 */
		createBalloon : function(styles, beforeDraw) {
			var balloon = new Kinetic.Shape({
				width: styles.width,
				height: styles.height,
				fill: styles.background.color,
				stroke: styles.border.color,
				strokeWidth: styles.border.thickness,
				lineCap: 'round',
				drawFunc: function(canvas) {
					if(beforeDraw) beforeDraw();
					
					var ctx = canvas.getContext();
					
					var tail = this.getTail(),
						tailWidth = this.getTailWidth(),
						tailHeight = this.getTailHeight();

					var	x = 0, y = (tail == 'top-left' || tail == 'top-right')? tailHeight : 0, 
						w = this.getWidth(), h = this.getHeight() - tailHeight,
						r = x + w, b = y + h,
						radius = (w > 40 && h > 40)? 20 : Math.min(w, h)/2;

					ctx.beginPath();

					// Draw Top Tails
					if(tail == 'top-left') {
						ctx.moveTo(x+radius, y);
						ctx.lineTo(x+(radius/2), y-tailHeight);
						ctx.lineTo(x+(radius*2), y);
					}
					else if(tail == 'top-right') {
						ctx.moveTo(r-(radius*2), y);
						ctx.lineTo(r-(radius/2), y-tailHeight);
						ctx.lineTo(r-radius, y);
					}
					else {
						ctx.moveTo(x+radius, y);
					}

					// Draw Right Border
					ctx.lineTo(r-radius, y);
					ctx.quadraticCurveTo(r, y, r, y+radius);
					ctx.lineTo(r, y+h-radius);
					ctx.quadraticCurveTo(r, b, r-radius, b);

					// Draw Bottom Tails
					if(tail == 'bottom-left') {
						ctx.lineTo(x+(radius*2), b);
						ctx.lineTo(x+(radius/2), b+tailHeight);
						ctx.lineTo(x+radius, b);
					}
					else if(tail == 'bottom-right') {
						ctx.lineTo(r-radius, b);
						ctx.lineTo(r-(radius/2), b+tailHeight);
						ctx.lineTo(r-(radius*2), b);
					}

					// Draw Left Border
					ctx.lineTo(x+radius, b);
					ctx.quadraticCurveTo(x, b, x, b-radius);
					ctx.lineTo(x, y+radius);
					ctx.quadraticCurveTo(x, y, x+radius, y);
					ctx.closePath();

					canvas.fill(this);
					canvas.stroke(this);
				}
			});

			balloon.attrs.tail = styles.tail;
			
			balloon.setInnerHeight = function(value) {
				this.setHeight(value / 0.9);
			}

			balloon.getTail = function() {
				return this.attrs.tail;
			}

			balloon.setTail = function(value) {
				this.attrs.tail = value;
			}
			
			balloon.getTailWidth = function() {
				return this.getWidth() * 0.025;
			}
			
			balloon.getTailHeight = function() {
				return this.getHeight() * 0.1;
			}
			
			return balloon;
		},
		
		
		/**
		 * Highlights a RGB Color
		 *
		 * @param string hex - the color in trihex format.
		 * @return string
		 */
		highlight : function(hex) {
			var rgb = E.colors.hex2rgb(hex);
			var hsl = E.colors.rgb2hsl(rgb[0], rgb[1], rgb[2]);
			hsl[2]+= (hsl[2] <= 0.8)? 0.2 : -0.2;
			rgb = E.colors.hsl2rgb(hsl[0], hsl[1], hsl[2]);
			return 'rgb(' + Math.round(rgb[0]) + ',' + Math.round(rgb[1]) + ',' + Math.round(rgb[2]) + ')';
		},
		
		
		/**
		 * Converts Seconds into mm:ss format.
		 *
		 * @param int secs - the seconds value.
		 * @return string
		 */
		secondsToMinutes : function(secs) {
			var mins = Math.floor(secs / 60);
		   	secs = secs % 60;

		   	return (mins < 10 ? "0" + mins : mins) + ":" + (secs < 10 ? "0" + secs : secs);
		},
		
		
		/**
		 * Get Collection by Element Id
		 * 
		 * @param string id - the element id.
		 * @return string collection
		 */
		getCollectionById : function(id) {
			var prefix = id.substr(0, 2), ids = E.defaults.ids;
			for(var collection in ids) {
				if(prefix == ids[collection]) {
					return collection;
				}
			}
		},
		
		
		/**
		 * Set Cursor
		 * 
		 * @param string cursor - the cursor name.
		 * @param string selector - the elements to apply the cursor.
		 */
		setCursor : function(cursor, selector) {
			var dom = selector? $(selector) : $('body');
			
			if(cursor) {
				if(cursor == 'grab') {
					if($('body').hasClass('chrome')) {
						cursor = '-webkit-'+cursor;
					}
					else if($('body').hasClass('moz')) {
						cursor = '-moz-'+cursor;
					}
					else cursor = 'pointer';
				}
				
				dom.css('cursor', cursor);
			}
			else {
				dom.css('cursor', '');
			}
		},
		
		
		/**
		 * Set Sound Events
		 *
		 * @param HTMLElement dom - the audio or video dom element.
		 */
		setSoundEvents : function(dom) {
			var domData = $(dom).data(), sounds = E.game.sounds;
			if(dom.tagName == 'AUDIO' || dom.tagName == 'VIDEO') {
				if(typeof domData.events === 'undefined') {
					$(dom).bind('play', function() {
						sounds.setBackgroundVolume(true);
					});

					$(dom).bind('pause', function() {
						sounds.setBackgroundVolume(false);
					});

					domData.events = true;
				}
			}
		},
		
		
		/**
		 * Sets Screen Top Margin based on Parent Height
		 */
		setScreenTopMargin : function() {
			var container = E.game.getScreen();
			var margin = (container.parent().height() - container.height())/2;

			if(margin > 0) {
				container.css('marginTop', margin);
			}
			else {
				container.css('marginTop', '');
			}
		},
		
		
		/**
		 * Clear Stage
		 *
		 * Clears the stage and removes all activities doms from it.
		 */
		clearStage : function() {
			var stage = E.game.stage;
			$(stage.getContainer()).children('.activity').remove();
			stage.clear();
		},
		
		
		/**
		 * Resets Stage Content
		 * 
		 * Clears the stage and then removes all its children.
		 */
		resetStage : function() {
			var stage = E.game.stage;
			this.clearStage();
			stage.ids = {};
			stage.names = {};
			
			// Reset Layers
			var layers = stage.getChildren();
			for(var i = 0; i < layers.length; i++) {
				
				var children = layers[i].getChildren(), numOfChildren = children.length;
				for(var j = 0; j < numOfChildren; j++) {
					var id = children[0].getId();
					
					// Remove shape and dom from element
					if(typeof id !== 'undefined') {
						var collection = this.getCollectionById(id);

						if(typeof collection !== 'undefined') {
							var element = E.game[collection][id];
							if(element) {

								// Remove Events from Shape
								if(element instanceof E.game.Button || element instanceof E.game.Audio || element instanceof E.game.Video || element instanceof E.game.PDF) {
									element.shape.off('mouseover mouseout click');
								}
								
								// Remove Questions DOM
								else if(element instanceof E.game.Question) {
									delete element.dom;
								}
								
								delete element.shape;
							}
						}
					}
					
					// Destroy shape
					children[0].destroy();
				}
			}
		}
		
	};
	
	
	/**
	 * Colors Manipulation functions
	 *
	 * @package E.colors
	 * @author Bruno Sampaio
	 */
	E.colors = {
		
		/**
		 * Converts a Trihex code into RGB values
		 *
		 * @param string hex - the hexadecimal code (e.g. #FF00FF).
		 */
		hex2rgb : function(hex){
		    hex = parseInt(hex.substring(1), 16);
			
		    var r = (hex & 0xff0000) >> 16;
		    var g = (hex & 0x00ff00) >> 8;
		    var b = hex & 0x0000ff;
		    return [r, g, b];
		},
		
		
		/**
		 * Converts an RGB color value to HSL.
		 * Assumes r, g, and b are contained in the set [0, 255] and
		 * returns h, s, and l in the set [0, 1].
		 *
		 * @param int r - the red color value.
		 * @param int g - the green color value.
		 * @param int b - the blue color value.
		 * @return array - the HSL representation.
		 */
		rgb2hsl : function(r, g, b) {
		    r /= 255, g /= 255, b /= 255;
		    var max = Math.max(r, g, b), min = Math.min(r, g, b);
		    var h, s, l = (max + min) / 2;

		    if(max == min) {
		        h = s = 0; // achromatic
		    }
			else {
		        var d = max - min;
		        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
		        switch(max){
		            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
		            case g: h = (b - r) / d + 2; break;
		            case b: h = (r - g) / d + 4; break;
		        }
		        h /= 6;
		    }

		    return [h, s, l];
		},

		/**
		 * Converts an HSL color value to RGB.
		 * Assumes h, s, and l are contained in the set [0, 1] and
		 * returns r, g, and b in the set [0, 255].
		 *
		 * @param int h - the hue
		 * @param int s - the saturation
		 * @param int l - the lightness
		 * @return array - the RGB representation
		 */
		hsl2rgb : function(h, s, l) {
		    var r, g, b;

		    if(s == 0)  {
		        r = g = b = l; // achromatic
		    }
			else {
		        function hue2rgb(p, q, t) {
		            if(t < 0) t += 1;
		            if(t > 1) t -= 1;
		            if(t < 1/6) return p + (q - p) * 6 * t;
		            if(t < 1/2) return q;
		            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
		            return p;
		        }

		        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
		        var p = 2 * l - q;
		        r = hue2rgb(p, q, h + 1/3);
		        g = hue2rgb(p, q, h);
		        b = hue2rgb(p, q, h - 1/3);
		    }

		    return [r * 255, g * 255, b * 255];
		}
		
	};
	
});
