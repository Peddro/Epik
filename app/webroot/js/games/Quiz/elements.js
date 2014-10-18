/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	
	/* SCENARIO FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Scenario.prototype = {

		createShape : function() {
			this.shape = new Kinetic.Rect({ id: this.id, x: 0, y: 0, fill: this.styles.background.color });
		},
		
		getType : function() {
			return this.type;
		},
		
		getHelps : function() {
			return ('helps' in this.rules)? this.rules.helps : E.game.scores.getScoreOfType('helps').value;
		},
		
		setHelps : function(value) {
			if(value <= E.game.scores.getScoreOfType('helps').value) {
				this.rules.helps = value;
			}
		},
		
		getBonus : function(type) {		
			if(typeof this.rules.bonus !== 'undefined') {
				var bonus = this.rules.bonus[type];
				if(typeof bonus != 'undefined') {
					return bonus.value;
				}
			}
			return 0;
		},
		
		setBonus : function(value, type) {
			if(typeof this.rules.bonus === 'undefined' && typeof E.defaults.rules[this.type].bonus !== 'undefined') {
				this.rules.bonus = {};
			}
			
			if(typeof this.rules.bonus !== 'undefined') {
				if(typeof this.rules.bonus[type] === 'undefined') {
					this.rules.bonus[type] = { value : 0, log : 0 };
				}
				this.rules.bonus[type].value = value;
			}
		},
		
		getBonusLog : function(type) {
			if(typeof this.rules.bonus != 'undefined') {
				var bonus = this.rules.bonus[type];
				if(typeof bonus == 'undefined') {
					return 0;
				}
				else return bonus.log;
			}
		},
		
		setBonusLog : function(value, type) {
			if(typeof this.rules.bonus != 'undefined') {
				if(typeof this.rules.bonus[type] != 'undefined') {
					this.rules.bonus[type].log = value;
				}
			}
		},
		
		getJump : function(type) {
			var jump = this.jumps[type];
			if(typeof jump == 'undefined') {
				return null;
			}
			else return jump;
		},
		
		setJumpTo : function(value, type) {
			if(typeof this.jumps[type] == 'undefined') {
				this.jumps[type] = {};
			}
			
			if(value) {
				if(value != 1) {
					this.jumps[type].to = value;
				}
				else if(typeof this.jumps[type].to != 'undefined') {
					delete this.jumps[type].to;
				}
			}
			else this.removeJump(type);
		},
		
		setJumpOn : function(value, type) {
			if(typeof this.jumps[type] == 'undefined') {
				this.jumps[type] = {};
			}
			this.jumps[type].on = value;
		},
		
		removeJump : function(type) {
			if(type in this.jumps) {
				delete this.jumps[type];
			}
		},

		draw : function(redraw, applyToShape) {
			var stage = E.game.stage, layers = E.game.layers, utils = E.game.utils;

			if(!redraw) utils.resetStage();

			// Add Background
			if(!redraw) {
				this.createShape();
				layers.background.add(this.shape);
			}
			this.shape.setSize(stage.getWidth(), stage.getHeight());

			// Iterate over scenario contents
			for(var i = 0; i < this.contents.length; i++) {
				var reference = this.contents[i], element = E.game[reference.collection][reference.id];

				// Add each Shape
				if(typeof element != 'undefined') {
					if(!redraw) {
						element.createShape();
						if(applyToShape) applyToShape(element);
						layers.elements.add(element.shape);
					}

					if(typeof element.shape != 'undefined') {
						
						// Set Shape Position
						utils.setShapePosition(layers.elements, element, reference.position);
						
						// Set Sprites Animation
						if(element.shape instanceof Kinetic.Sprite) {
							element.animate();
						}
					}
				}
			}

			// Draw Everything
			stage.draw();
		}
		
	}
	$.extend(E.game.Scenario.prototype, E.game.Element.prototype);
	
	
	/* HEADING FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Heading.prototype = {
		
		createShape : function() {
			this.shape = E.game.utils.createTextBox(this.id, this.text, 'Galindo, cursive', 20, 1.5, 'center', this.styles);
		}
		
	};
	$.extend(E.game.Heading.prototype, E.game.Text.prototype);
	
	
	/* PARAGRAPH FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Paragraph.prototype = {
		
		createShape : function() {
			this.shape = E.game.utils.createTextBox(this.id, this.text, 'Andika, sans-serif', 10, 1.6, 'left', this.styles);
		}
		
	};
	$.extend(E.game.Paragraph.prototype, E.game.Text.prototype);
	
	
	/* BUTTON FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Button.prototype = {
		
		createShape : function() {
			this.shape = E.game.utils.createTextBox(this.id, this.text, 'Fredoka One, cursive', 15, 1.3, 'center', this.styles);
			this.shape.setName(this.type);
			this.events();
		},
		
		events : function() {}
		
	};
	$.extend(E.game.Button.prototype, E.game.Text.prototype);
	
	
	/* LINE FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Line.prototype = {
		
		createShape : function() {
			this.shape = new Kinetic.Line({
				id: this.id,
				width: this.styles.length,
				fill: 'transparent',
				stroke: this.styles.border.color,
				strokeWidth: this.styles.border.thickness,
				rotationDeg: this.styles.rotation,
				lineCap: 'round',
				dragBoundFunc: E.game.utils.dragBounds
			});
		}
		
	};
	$.extend(E.game.Line.prototype, E.game.Element.prototype);
	
	
	/* SQUARE FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Square.prototype = {
		
		createShape : function() {
			this.shape = E.game.utils.createTextBox(this.id, this.text, 'Andika, sans-serif', 10, 1.6, 'left', this.styles);
		}
		
	};
	$.extend(E.game.Square.prototype, E.game.Text.prototype);
	
	
	/* CIRCLE FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var adjustCircleText = function(shape, text) {
		var size = Math.sqrt(Math.pow(shape.getWidth(), 2)/2);
		text.setSize(size, size);
		text.setOffset(size/2, size/2);
	}
		
	E.game.Circle.prototype = {
		
		createShape : function() {
			this.shape = new Kinetic.Group({ 
				id: this.id,
				width: (this.styles.radius*2) + this.styles.border.thickness,
				height: (this.styles.radius*2) + this.styles.border.thickness,
				dragBoundFunc: E.game.utils.dragBounds
			});
			
			var shape = new Kinetic.Circle({
				name: 'shape',
				radius: this.styles.radius,
				fill: this.styles.background.color,
				stroke: this.styles.border.color,
				strokeWidth: this.styles.border.thickness
			});
			
			var text = new Kinetic.Text({
				name: 'text',
				fontFamily: 'Andika, sans-serif',
				fontSize: this.styles.font.size,
				fontStyle: this.styles.font.style,
				fill: this.styles.font.color,
				width: this.styles.radius,
				height: this.styles.radius,
				text: this.text,
				padding: 10,
				lineHeight: 1.6,
				align: 'left'
			});
			
			adjustCircleText(shape, text);
			
			this.shape.add(shape);
			this.shape.add(text);
		},
		
		getRadius : function() {
			return this.styles.radius;
		},
		
		setRadius : function(value) {
			this.shape.setSize(value*2, value*2);
			
			var circle = this.getChildrenOfType('.shape');
			var text = this.getChildrenOfType('.text');
			circle.setRadius(value);
			
			adjustCircleText(circle, text);
			
			this.styles.radius = value;
		}
		
	};
	$.extend(E.game.Circle.prototype, E.game.Text.prototype);
	
	
	/* BALLOON FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var adjustBalloonText = function(shape, text) {
		if(shape.getTail() == 'top-left' || shape.getTail() == 'top-right') {
			text.setY(shape.getTailHeight());
		}
		else text.setY(0);
		text.setHeight(shape.getHeight() - shape.getTailHeight());
	}
	
	E.game.Balloon.prototype = {
		
		createShape : function() {
			this.shape = new Kinetic.Group({ 
				id: this.id,
				width: this.styles.width,
				height: this.styles.height,
				rotationDeg: this.styles.rotation,
				dragBoundFunc: E.game.utils.dragBounds
			});

			var shape = E.game.utils.createBalloon(this.styles);
			shape.setName('shape');

			var text = new Kinetic.Text({
				name: 'text',
				fontFamily: 'Andika, sans-serif',
				fontSize: this.styles.font.size,
				fontStyle: this.styles.font.style,
				fill: this.styles.font.color,
				width: this.styles.width,
				height: this.styles.height,
				text: this.text,
				padding: 12,
				lineHeight: 1.6,
				align: 'left'
			});

			adjustBalloonText(shape, text);

			this.shape.add(shape);
			this.shape.add(text);
		},
		
		getTail : function() {
			return this.styles.tail;
		},

		setTail : function(value) {
			var children = this.shape.getChildren();
			children[0].setTail(value);
			adjustBalloonText(children[0], children[1]);
			this.styles.tail = value;
		}
		
	};
	$.extend(E.game.Balloon.prototype, E.game.Text.prototype);
	
	E.game.Balloon.prototype.setHeight = function(value) {
		var children = this.shape.getChildren();
		this.shape.setHeight(value);
		children[0].setHeight(value);
		adjustBalloonText(children[0], children[1]);
		this.styles.height = value;
	}

	
	/* RESOURCE FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	$.extend(E.game.Resource.prototype, {
		
		createShape : function() {
			if(this.image) {
				var params = {
					id: this.id,
					image: this.image,
					width: this.styles.width,
					height: this.styles.height,
					stroke: this.styles.border.color,
					strokeWidth: this.styles.border.thickness,
					rotationDeg: this.styles.rotation,
					dragBoundFunc: E.game.utils.dragBounds
				};
				
				// Set Crop Area
				if(!(this instanceof E.game.Image) && this.source) {
					params.crop = this.getCrop();
				}
				
				this.shape = new Kinetic.Image(params);

				// If no width and height styles provided
				if(!this.styles.width && !this.styles.height) {
					var layer = E.game.layers.elements;

					// If image width greater than layer width
					if(this.image.width > layer.getWidth()) {
						var originalWidth = this.image.width;
						this.image.width = layer.getWidth()*0.5;
						this.image.height = (this.image.height * this.image.width) / originalWidth;
					}

					// If image height greater than layer height
					if(this.image.height > layer.getHeight()) {
						var originalHeight = this.image.height;
						this.image.height = layer.getHeight()*0.5;
						this.image.width = (this.image.width * this.image.height) / originalHeight;
					}

					// Set Sizes
					this.setWidth(this.image.width);
					this.setHeight(this.image.height);
				}
			}
		},
		
		getCrop : function() {
			var crop = { x: 0, y: 0, width: this.image.height, height: this.image.height };
			if(this instanceof E.game.Video) {
				crop.x = this.image.height * 2;
			}
			return crop;
		},
		
		setData : function(value) {
			var isImage = this instanceof E.game.Image;
			if(this.source) {
				this.image = isImage? value.object : E.defaults.resources[value].object;
			}
			else this.image = E.defaults.resources['no'+value].object;
			
			if(typeof this.shape != 'undefined') {
				this.shape.setImage(this.image);
				if(this.source && !isImage) {
					this.shape.setCrop(this.getCrop());
				}
			}
		}
	});
	
	$.extend(E.game.Audio.prototype, E.game.Resource.prototype);
	$.extend(E.game.Image.prototype, E.game.Resource.prototype);
	$.extend(E.game.Video.prototype, E.game.Resource.prototype);
	$.extend(E.game.PDF.prototype, E.game.Resource.prototype);
	
	
	/* QUESTION FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var adjustQuestionDOM = function(shape, dom, styles) {
		var pos = shape.getPosition(), layersPos = E.game.layers.elements.getPosition(), scale = E.game.stage.getScale(), padding = 10, hidden = !dom.parent().is(':visible');
		
		if(hidden) {
			dom.parent().css({ visibility : 'hidden', display : 'block' });
		}
		
		dom.width(shape.getWidth() - (padding * 2));
		shape.setInnerHeight(dom.height() + (padding * 2));
		
		var domStyles = { 
			left: (pos.x - (shape.getWidth()/2)) + layersPos.x + padding, 
			top: (pos.y - (shape.getHeight()/2)) + layersPos.y + padding, 
			zoom : scale.x
		};
		
		// If ballon tail is on the top
		if(shape.getTail() == 'top-left' || shape.getTail() == 'top-right') {
			domStyles.top+= shape.getTailHeight();
		}
		dom.css(domStyles);
		
		if(hidden) {
			dom.parent().css({ visibility : '' });
		}
	};
	
	E.game.Question.prototype = {
		
		createShape : function() {
			if(this.source) {
				var self = this, types = E.defaults.types.question;
				
				// Create Answers DOM
				var answers = '<form>';
				if(this.type == types[1]) {
					answers+= '<input name="'+this.id+'" type="text" />';
				}
				else if(this.type == types[2] || this.type == types[3]) {
					answers+= '<table>';
					
					var rows = '';
					for(var id in this.answers) {
						var answer =
							'<tr>'+
								'<td><input id="'+this.id+id+'" name="'+this.id+'" type="radio" value="'+id+'" /></td>'+
								'<td><label for="'+this.id+id+'">'+this.answers[id]+'</label></td>'+
							'</tr>';
							
						if(Math.random() > 0.5) rows+= answer;
						else rows = answer + rows;
					}
					answers+= rows + "</table>";
				}
				answers+= '</form>';

				// Create Info DOM
				var info = 
					'<div class="points"><b>' + E.strings.labels.points + '</b>: <span class="value">' + this.scores.reward.value + '</span></div>'+
					'<div class="helps"><b>' + E.strings.labels.helps + '</b>:</div>';

				// Create DOM
				this.dom = 
					$('<div class="activity">' +
						'<div class="question">' + this.question + '</div>' +
						'<div class="answers">' + answers + '</div>' +
						'<div class="info">' + info + '</div>' +
					'</div>');
				
				// Add Helps Images to DOM
				var container = this.dom.children('.info').children('.helps');
				var resources = E.defaults.resources, strings = E.strings.labels.help;
				var helpsVisible = 0;
				
				$.each(this.helps, function(helpType, value) {
					var img = $(resources[helpType].object).clone().addClass(helpType).attr('title', strings[helpType]).tipTip();
					container.append(img);
					img.wrap('<div/>');
					helpsVisible+= self.setHelpVisible(value.use, img);
				});
				
				if(!helpsVisible) container.hide();
			}
			else {
				this.dom = $('<div class="activity">'+E.strings.labels.noquestion+'</div>');
			}
			
			// Create Balloon
			var element = this;
			this.shape = E.game.utils.createBalloon(this.styles, function() {
				adjustQuestionDOM(element.shape, element.dom, element.styles);
			});
			this.shape.setId(this.id);
			this.shape.setDragBoundFunc(E.game.utils.dragBounds);
			
			$(E.game.stage.getContainer()).append(this.dom);
			adjustQuestionDOM(this.shape, this.dom, this.styles);
			this.setRotation(-this.styles.rotation);
			
			// Set Events
			this.events();
			this.solved = false;
		},
		
		getTail : function() {
			return this.styles.tail;
		},

		setTail : function(value) {
			this.shape.setTail(value);
			this.styles.tail = value;
		},
		
		isGroup : function(value) {
			if(typeof value != 'undefined') {
				this.group = value;
			}
			return this.group;
		},
		
		setData : function(data) {
			this.setType(data.type);
			this.setQuestion(data.question);
			this.setAnswers(data.answers);
			
			if(typeof data.helps != 'undefined') {
				var allowed = E.defaults.helps.question[data.type];
				for(var helpType in this.helps) {
					if(!(helpType in allowed) || ((helpType in data.helps) && this.helps[helpType].use && !data.helps[helpType])) {
						this.helps[helpType].use = 0;
					}
				}
			}
		},
		
		setType : function(value) {
			this.type = value;
		},
		
		setScore : function(value, type) {
			this.scores[type].value = value;
			if(type == 'reward') {
				this.dom.find('.points .value').text(value);
			}
		},
		
		setLog : function(value, type) {
			if(typeof this.scores[type] != 'undefined') {
				this.scores[type].log = value;
			}
		},
		
		setHelps : function(value) {
			this.helps = value;
		},
		
		getHelpUse : function(type) {
			return this.helps[type].use;
		},
		
		setHelpUse : function(value, type) {
			if(typeof this.helps[type] != 'undefined') {
				this.helps[type].use = value;
				
				// Show/Hide Helps DOM and a Help Icon
				if(typeof this.dom != 'undefined') {
					var helpsDOM = this.dom.find('.helps');
					
					if(this.setHelpVisible(value, helpsDOM.find('.'+type))) {
						helpsDOM.show();
					}
					else if(helpsDOM.find('img:visible').length == 0) {
						helpsDOM.hide();
					}
				}
			}
		},
		
		getHelpSelected : function(type) {
			return this.helps[type].selected;
		},
		
		setHelpSelected : function(value, type) {
			if(typeof this.helps[type] != 'undefined') {
				this.helps[type].selected = $.isEmptyObject(value)? 0 : value;
			}
		},
		
		setHelpVisible : function(value, img) {
			if(value) {
				img.parent().css('display', '');
				return 1;
			}
			else {
				img.parent().hide();
				return 0;
			}
		},
		
		setQuestion : function(value) {
			this.question = value;
		},
		
		setAnswers : function(value) {
			this.answers = value;
		},
		
		getAnswerInput : function(id) {
			value = id? '[value='+id+']' : '';
			return this.dom.find('.answers input'+value);
		},
		
		disableAnswer : function(id) {
			return this.getAnswerInput(id).attr(classes.disabled, true);
		}
		
	};
	$.extend(E.game.Question.prototype, E.game.Activity.prototype);
		
});
