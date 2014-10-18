$(document).ready(function() {
	
	/**
	 * Sets the Form for the Current Element Selected and Binds the Fields Events
	 *
	 * This method first creates and adds the form to the properties panel.
	 * Then the general events are set, such as, TipTip pop-ups, form submit, and modal links.
	 *
	 * After that, it checks three cases:
	 * - If 'element' is a game element, and it is locked the fields are disabled,
	 *   and if it is a template element (no source) an event is bound to the choose button.
	 * - If 'element' is a scenario, the not allowed jump types are disabled.
	 * - If 'element' is a property, or more precisely the sounds property, the choose and remove events are set.
	 * For each case the elementId and collection variables are also set.
	 *
	 * Afterwards, it binds input/change events to text fields, number fields, color fields, select fields, checkboxes, radio buttons, and buttons.
	 * For each field type, when an event is triggered, an action is performed over the current element based on the field id.
	 * All possible ids fields are found on E.selectors.ids.fields.
	 *
	 * @param E.game.Scenario scenario - the scenario object (null for properties).
	 * @param E.game.Element/Property element - the current element selected.
	 * @param object reference - the element reference object on this scenario.
	 */
	E.sections.properties.setForm = function(scenario, element, reference) {
		var self = this,
		form = E.html.createPropertiesForm(element, (typeof reference != 'undefined' && reference)? reference.position : null),
		notProperty = element.parent != 'property',
		notScenario = !(element instanceof E.game.Scenario),
		fields = ids.fields,
		shape = notProperty? element.shape : null, 
		layer = shape? shape.getLayer() : null,
		utils = E.game.utils;
		
		// Add form to document
		var section = $('#'+ids.properties+' .'+classes.list);
		section.html(form);
		form = section.children('form');
		
		// Set Form General Events
		form.find('*[title]').tipTip({maxWidth: '100px', defaultPosition: 'left'});
		form.submit(function(event) { event.preventDefault(); });
		E.modal.apply(form.find('.'+ids.modal));
		
		// If is a element
		var elementId, collection, position;
		if(notProperty && notScenario) {
			if(element.isLocked()) this.changeFormFieldsStatus(true);
			
			elementId = element.getId();
			collection = reference.collection;
			position = reference.position;
			
			if(typeof element.source != 'undefined' && !element.source) {
				form.find('#'+fields.actions.general+' .button').click(function() {
					var type = $(this).attr('class').split(' ')[3];
					E.tools.startInsert(elementId, collection, type);
				});
			}
		}
		
		// If is a scenario
		else if(!notScenario) {
			elementId = scenario.getId();
			collection = E.defaults.collections[0];
		}
		
		// If is a property
		else if(!notProperty) {
			
			elementId = element.getIcon();
			collection = null;
			
			if(element instanceof E.game.Sounds) {
				form.children().each(function(key, value) {
					var sound = $(value).children('label').attr('for').split('-')[2];

					$(value).children('.button').click(function() {
						E.tools.startInsert(null, E.defaults.collections[3], sound, element.getIcon());
					});

					$(value).children('.remove').click(function() {
						element.setSourceId(0, sound);
						$(value).children('audio').remove();
						$(value).append(element.getFile(sound));
						E.tools.setSaveData(elementId, collection, 0);
					});

					var file = element.getFile(sound);
					if(file) $(value).append(file);
				});
			}
		}
		
		// Text Fields Event
		form.find('input[type=text], textarea').bind('input', function(event) {
			var id = $(this).attr('id'),
				value = $(this).val(),
				params = id.split('-'),
				operation = -1;
			
			switch(true) {
				case (id == fields.name):
					if(value.length > 0) {
						currentProject.element.children('.name').text(value);
						element.setName(value);
						operation = 1;
					}
					break;
					
				case (id == fields.content):
					element.setText(value);
					operation = 2;
					break;
					
				case id.startsWith(fields.scores.general) && id.endsWith('name'):
					if(value.length > 0) {
						element.setName(value, params[2]);
						operation = 0;
					}
					break;
			}
			
			if(operation >= 0) E.tools.setSaveData(elementId, collection, operation);
			if(layer) layer.draw();
		});
		
		// Input Numeric Fields Event
		form.find('input[type=number]').bind('input', function(event) {
			var id = $(this).attr('id');
			var min = $(this).attr('min');
			var max = $(this).attr('max');
			var value = $(this).val();
			var length = value.length != 0;
			var operation = 0;
			
			if(!isNaN(value) && length) {
				var params = id.split('-');
				value = Math.abs(parseInt(value));
				
				// Check acceptable value ranges
				if(typeof min != 'undefined') {
					value = (value < min)? min : value;
				}
				if(typeof max != 'undefined') {
					value = (value > max)? max : value;
				}
				
				switch(true) {
					case (id == fields.dimensions.width): case (id == fields.dimensions.length):
						element.setWidth(value);
						operation = 6;
						break;
						
					case (id == fields.dimensions.height):
						element.setHeight(value);
						operation = 6;
						break;
						
					case (id == fields.dimensions.radius):
						element.setRadius(value);
						operation = 6;
						break;
						
					case (id == fields.border.thickness):
						var alphaInput = $('#'+fields.border.alpha);
						if(value <= 0) alphaInput.prop('checked', true).change();
						else alphaInput.prop('checked', false).change();
						element.setBorderThickness(value);
						operation = 6;
						break;
						
					case (id == fields.flow.ons.timeout):
						element.setJumpOn(value, params[2]);
						break;
						
					case (id == fields.playersNumber):
						element.setPlayersNumber(value);
						break;
						
					case id.startsWith(fields.position.absolute.general):
						position.absolute.point[params[4]] = value;
						operation = 5;
						break;
						
					case id.startsWith(fields.rules.bonus.general):
						element.setBonus(value, params[3]);
						break;
						
					case (id == fields.rules.helps):
						element.setHelps(value);
						break;
						
					case (id.startsWith(fields.scores.helps.general)  && id.endsWith('value')):
						element.setValue(value, params[2]);
						break;
						
					case (id.startsWith(fields.scores.general) && id.endsWith('value')):
						element.setScore(value, params[2]);
						break;
						
					case (id == fields.transformations.rotation):
						element.setRotation(value);
						operation = 6;
						break;
				}
				
				if(operation == 5) {
					E.tools.setSaveData(scenario.getId(), E.defaults.collections[0], operation, elementId);
				}
				else E.tools.setSaveData(elementId, collection, operation);
				
				if(layer && notScenario) {
					utils.setShapePosition(layer, element, position);
					E.anchors.move(shape, true);
					layer.draw();
				}
			}
			else if(length) {
				$(this).val(0);
				alert(E.strings.errors.numberCode);
			}
		});
		
		// Input Color Fields Event
		form.find('input[type=color]').bind('input', function(event) {
			var id = $(this).attr('id');
			var value = $(this).val();
			var params = id.split('-');
			var field = params[params.length-1];
			var operation = 6;
			
			switch(true) {
				case id.startsWith(fields.background.color):
					element.setBackgroundColor(value, field);
					break;
					
				case id.startsWith(fields.border.color):
					element.setBorderColor(value, field);
					break;
					
				case id.startsWith(fields.font.color):
					element.setFontColor(value, field);
					break;
			}
			
			E.tools.setSaveData(elementId, collection, operation);
			if(layer) layer.draw();
		});
		
		
		// Select Fields Event
		form.find('select').bind('change', function(event) {
			var id = $(this).attr('id');
			var value = ($(this).val() == '0')? 0 : $(this).val();
			var params = id.split('-');
			var operation = -1;
			
			var draw = false;
			switch(true) {
				case (id == fields.font.size):
					element.setFontSize(value);
					operation = 6;
					draw = true;
					break;
				
				case (id == fields.font.style):
					element.setFontStyle(value);
					operation = 6;
					draw = true;
					break;
					
				case id.startsWith(fields.flow.general):
					var jumpType = params[2];
					
					// Add/Remove Button
					if(typeof E.defaults.contents.button[jumpType] != 'undefined') {
						self.addRemoveJumpButton(element, jumpType, value);
					}
					
					// Set Jump To
					element.setJumpTo(value, jumpType);
					
					// Set Jump On
					if(value && typeof fields.flow.ons[jumpType] != 'undefined') {
						form.find('#'+fields.flow.ons[jumpType]).trigger('input');
					}
					
					operation = 0;
					break;
					
				case id.startsWith(fields.helps.general):
					var sourcesSelected = {};
					
					if(value instanceof Array) {
						if(value.length > 0) {
							for(var i = 0; i < value.length; i++) {
								sourcesSelected[value[i]] = 1;
							}
						}
					}
					else sourcesSelected[value] = 1;
					
					element.setHelpSelected(sourcesSelected, params[2]);
					operation = 0;
					break;
				
				case (id == fields.position.type):
					self.switchPositionType(form, value, shape, position, fields);
					draw = true;
					break;
					
			}
			
			if(operation >= 0) E.tools.setSaveData(elementId, collection, operation);
			
			if(layer && draw) {
				utils.setShapePosition(layer, element, position);
				E.anchors.move(shape, true);
				layer.draw();
			}
		});
		
		
		// Checkboxes Fields Event
		form.find('input[type=checkbox]').bind('change', function(event) {
			var id = $(this).attr('id');
			var value = $(this).is(':checked')? 1 : 0;
			var params = id.split('-'), field = params[params.length-1];
			var operation = -1;
			
			var draw = true;
			switch(true) {
				case id.startsWith(fields.border.alpha):
					self.setColorType(fields.border.color, element, params[1], value, field);
					operation = 6;
					break;
					
				case id.startsWith(fields.background.alpha):
					self.setColorType(fields.background.color, element, params[1], value, field);
					operation = 6;
					break;
					
				case (id == fields.flow.start):
					E.game.start = (value)? elementId : false;
					draw = false;
					break;
					
				case id.startsWith(fields.rules.general) && id.endsWith('log'):
					element.setBonusLog(value, params[3]);
					operation = 0;
					draw = false;
					break;
					
				case id.startsWith(fields.scores.general) && id.endsWith('log'):
					element.setLog(value, params[2]);
					operation = 0;
					draw = false;
					break;
					
				case id.startsWith(fields.helps.general):
					element.setHelpUse(value, params[2]);
					if(value) {
						var select = form.find('#'+fields.helps[params[2]].selected);
						if(select.length > 0 && select.find('[selected]').length == 0) {
							select.find('option:first').attr('selected', true);
						}
						select.trigger('change');
					}
					operation = 0;
					break;
			}
			
			if(operation >= 0) E.tools.setSaveData(elementId, collection, operation);
			if(layer && draw) layer.draw();
		});
		
		
		// Radio Buttons Labels Event
		form.find('.input.radio label').click(function(event) {
			event.preventDefault();
			
			var id = $(this).attr('for'), input = $(this).siblings('#'+id);
			if(!input.is(':disabled')) {
				$(this).siblings('label').removeClass('selected');
				input.prop('checked', true).change();
				$(this).addClass('selected');
			}
		});
		
		// Radio Buttons Fields Event
		form.find('input[type=radio]').bind('change', function(event) {
			var id = $(this).attr('id');
			var value = $(this).val();
			var operation = 0;
			
			if(id.startsWith(fields.position.aligned.horizontal)) {
				position.aligned.horizontal = value;
				operation = 5;
			}
			else if(id.startsWith(fields.position.aligned.vertical)) {
				position.aligned.vertical = value;
				operation = 5;
			}
			else if(id.startsWith(fields.position.corner)) {
				element.setCorner(value);
				operation = 6;
			}
			else if(id.startsWith(fields.position.side)) {
				element.setSide(value);
				operation = 6;
			}
			else if(id.startsWith(fields.tail.general)) {
				element.setTail(value);
				operation = 6;
			}
			
			if(operation == 5) {
				E.tools.setSaveData(scenario.getId(), E.defaults.collections[0], operation, elementId);
			}
			else E.tools.setSaveData(elementId, collection, operation);
			
			if(layer) {
				utils.setShapePosition(layer, element, position);
				E.anchors.move(shape, true);
				layer.draw();
			}
		});
		
		
		// Buttons Fields Event
		form.find('button').bind('click', function(event) {
			if(!$(this).attr('disabled')) {
				var id = $(this).attr('id');

				if(id.startsWith(fields.position.z.general)) {
					self.changeZIndex(scenario, shape, id);
				}
			}
		});
	}
	
});