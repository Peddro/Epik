$(document).ready(function() {
	
	/**
	 * HTML functions
	 *
	 * @package E.html
	 * @author Bruno Sampaio
	 */
	E.html = {
		
		/**
		 * Creates a Div
		 *
		 * @param string id - the content for the id attribute (optional).
		 * @param string classes - the content for the class attribute (optional).
		 * @param string title - the content for the title attribute (optional).
		 * @return string
		 */
		createDiv : function(id, classes, title, content) {
			id = id? 'id="'+id+'"' : '';
			classes = classes? 'class="'+classes+'"' : '';
			title = title? 'title="'+title+'"' : '';
			return '<div '+id+' '+classes+' '+title+'>'+content+'</div>';
		},
		
		
		/**
		 * Creates a Scenario Item
		 *
		 * @param string id - the scenario id.
		 * @param string name - the scenario name.
		 * @param string contents - the scenario contents list.
		 * @param string type - the scenario type.
		 * @return string
		 */
		createScenarioItem : function(id, name, type, contents) {
			var item = 
				$('<li id="'+id+'">'+
					this.createDiv(false, 'arrow', false, '')+
					this.createDiv(false, classes.icons.scenario+' '+type+' item', false, 
						this.createDiv(false, 'icon-small', false, '') + this.createDiv(false, 'name', false, name)
					)+
					'<ul class="collections">'+
						this.createCollections(contents, type)+
					'</ul>'+
				'</li>');
				
			$('#'+ids.explorer+' .'+classes.list+' .scenarios').append(item);
			return item;
		},
		
		
		/**
		 * Creates a Collection Item
		 *
		 * @param string contents - the scenario contents list.
		 * @param string type - the scenario type.
		 * @return string
		 */
		createCollections : function(contents, type) {
			var allowsActivities = E.scenarios.allowsActivities(type),
				strings = E.strings.collections,
				collections = {}, 
				game = E.game;
			
			// Set collections
			for(var key in strings) {
				if(allowsActivities || key != E.defaults.collections[4]) {
					collections[key] = '';
				}
			}
			
			// Iterate over contents and place them on correct collections
			for(var i = 0; i < contents.length; i++) {
				var reference = contents[i], element = game[reference.collection][reference.id];
				if(typeof element != 'undefined') {
					collections[reference.collection]+= this.createElementItem(element.getId(), element.getName(), element.getIcon());
				}
			}
			
			var html = '';
			for(var key in collections) {
				html+=
					'<li class="'+key+'">'+
						this.createDiv(false, 'arrow', false, '')+
						this.createDiv(false, 'collection item closed', false, 
							this.createDiv(false, 'icon-small', false, '') + this.createDiv(false, 'name', false, strings[key])
						)+
						'<ul class="elements">'+
							collections[key]+
						'</ul>'+
					'</li>';
			}	
			return html;
		},
		
		
		/**
		 * Creates a Element Item
		 *
		 * @param string id - the element id.
		 * @param string name - the element name.
		 * @param string contents - the element icon.
		 * @return string
		 */
		createElementItem : function(id, name, icon) {
			return '<li id="' + id + '" class="element item '+icon+'">' + this.createDiv(false, 'icon-small', false, '') + this.createDiv(false, 'name', false, name) + '</li>';
		},
		
		
		/**
		 * Creates a Paragraph Item
		 *
		 * @param string id - the item id (optional) or content.
		 * @param string classes - the item classes (optional).
		 * @param string content - the item content (if not provided, the first argument is considered).
		 * @return string
		 */
		createParagraph : function(id, classes, content) {
			if(id && !content) {
				content = id;
				id = null;
			}
			id = id? 'id="'+id+'"' : '';
			classes = classes? 'class="'+classes+'"' : '';
			
			return '<p '+id+' '+classes+'>'+content+'</p>';
		},
		
		
		/**
		 * Creates a Modal Button Link
		 *
		 * Creates a link with button format and modal class.
		 * Used to open a Modal Window when clicked.
		 *
		 * @param string name - the button text content.
		 * @param string classes - the button extra classes besides: button, silver, and modal (optional).
		 * @param string url - the link url to be requested when clicked.
		 * @return string
		 */
		createModalButtonLink : function(name, classes, url) {
			classes = classes || '';
			return '<a href="'+url+'" class="button silver modal '+classes+'">'+name+'</a>';
		},
		
		
		/**
		 * Creates a Modal Icon Link
		 *
		 * Creates a link with an icon inside and with modal class.
		 * Used to open a Modal Window when clicked.
		 *
		 * @param string title - the link title.
		 * @param string icon - the icon name.
		 * @param string url - the link url to be requested when clicked.
		 * @param string size - the icon size: small or big (optional).
		 * @return string
		 */
		createModalIconLink : function(title, icon, url, size) {
			size = size? '-'+size : '';
			return E.html.createDiv(false, icon, title, '<a href="'+url+'" class="icon'+size+' modal"></a>');
		},
		
		
		/**
		 * Creates an Icon
		 *
		 * Creates a div with an icon as background image.
		 *
		 * @param string name - the icon name.
		 * @param string title - the div title.
		 * @param string url - the icon size: small or big (optional).
		 * @return string
		 */
		createIcon : function(name, title, size) {
			size = size? '-'+size : '';
			return this.createDiv(false, name, title, '<div class="icon'+size+'"></div>');
		},
		
		form: {
			
			/**
			 * Creates a Label
			 *
			 * @param string id - the field id, which is associated to this label.
			 * @param string label - the label text content.
			 * @param string title - the label title (optional).
			 * @param string classes - the label classes (optional).
			 * @return string
			 */
			createLabel: function(id, label, title, classes) {
				title = title? 'title="'+title+'"' : '';
				classes = classes? 'class="'+classes+'"' : '';
				return '<label for="'+id+'" '+title+' '+classes+'>'+label+'</label>';
			},
			
			
			/**
			 * Creates an Input Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param string type - the field type.
			 * @param bool required - determines if the field is required.
			 * @param string value - the field value.
			 * @param string unit - the field unit (optional).
			 * @param string extra - field extra attributes.
			 * @return string
			 */
			createInput: function(id, label, title, type, required, value, unit, extra) {
				label = label? this.createLabel(id, label, title) : '';
				required = required? 'required' : '';
				unit = unit? '<span class="unit">'+unit+'</span>' : '';
				extra = extra? extra : '';
				
				var classes = 'input '+type+' '+required;
				return 	E.html.createDiv(false, classes, false, label + '<input id="'+id+'" type="'+type+'" '+extra+' name="'+id+'" value="'+value+'" />'+ unit);
			},
			
			
			/**
			 * Creates a Text Input Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param bool required - determines if the field is required.
			 * @param string value - the field value.
			 * @param int max - the maximum text length.
			 * @param string unit - the field unit (optional).
			 * @return string
			 */
			createTextInput: function(id, label, title, required, value, max, unit) {
				var extra = 'maxlength="' + max + '"';
				return this.createInput(id, label, title, 'text', required, value, unit, extra);
			},
			
			
			/**
			 * Creates a Number Input Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param bool required - determines if the field is required.
			 * @param string value - the field value.
			 * @param int min - the minimum value allowed.
			 * @param int max - the maximum value allowed.
			 * @param string unit - the field unit (optional).
			 * @return string
			 */
			createNumberInput: function(id, label, title, required, value, min, max, unit) {
				var extra = 'min="' + min + '" ' + 'max="' + max + '"';
				return this.createInput(id, label, title, 'number', required, value, unit, extra);
			},
			
			
			/**
			 * Creates a Color Input Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param bool required - determines if the field is required.
			 * @param string value - the field value.
			 * @param string disabled - determines if the field is disabled (optional).
			 * @return string
			 */
			createColorInput: function(id, label, title, required, value, disabled) {
				var extra = disabled? 'disabled' : '';
				return this.createInput(id, label, title, 'color', required, value, false, extra);
			},
			
			
			/**
			 * Creates a Select Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param bool required - determines if the field is required.
			 * @param string value - the selected value(s).
			 * @param object options - the options list.
			 * @param bool multiple - determines if the field allows multiple values.
			 * @param string extra - field extra attributes.
			 * @return string
			 */
			createSelect: function(id, label, title, required, value, options, multiple, extra) {
				label = label? this.createLabel(id, label, title) : '';
				required = required? 'required' : '';
				multiple = multiple? 'multiple' : '';
				extra = extra? extra : '';
				
				var classes = 'input select '+required;
				var opts = '';
				
				for(var option in options) {
					var selected = (option == value || value[option] == true)? 'selected="selected"' : '';
					opts+= '<option value="'+option+'" '+selected+'>'+options[option]+'</option>';
				}
				
				return 	E.html.createDiv(false, classes, false, label + '<select id="'+id+'" '+multiple+'>' + opts + '</select>' + extra);
			},
			
			
			/**
			 * Creates a Checkbox Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string title - the label title (optional).
			 * @param string checked - the selected value.
			 * @param bool required - determines if the field is required.
			 * @param bool left - determines if the label must be to the left or right (default is right).
			 * @param string disabled - determines if the field is disabled (optional).
			 * @return string
			 */
			createCheckbox: function(id, label, title, checked, required, left, disabled) {
				label = label? this.createLabel(id, label) : '';
				title = title? 'title="'+title+'"' : '';
				checked = checked? 'checked="checked"' : '';
				required = required? 'required' : '';
				disabled = disabled? 'disabled' : '';
				
				var input = '<input id="'+id+'" type="checkbox" name="'+id+'" '+title+' '+checked+' '+disabled+'>';
				var contents = left? label+input : input+label;
				return 	E.html.createDiv(false, 'input checkbox '+required, false, contents);
			},
			
			
			/**
			 * Creates a Radio Buttons Group
			 *
			 * @param string id - the general id for each button.
			 * @param string labels - the labels for each button.
			 * @param string prefix - options prefix.
			 * @param string checked - the selected value.
			 * @param bool options - the options list.
			 * @return string
			 */
			createRadioButtons: function(id, labels, prefix, checked, options) {
				var html = '';
				for(var option in options) {
					var currentId = id+'-'+option;
					var selected = (option == checked)? 'selected' : '';
						label = labels? this.createLabel(currentId, labels[option]) : this.createLabel(currentId, '<div class="icon"></div>', options[option], prefix+'-'+option+' '+selected);
					selected = (option == checked)? 'checked="checked"' : '';
					
					html+= label + '<input id="'+currentId+'" type="radio" name="'+id+'" value="'+option+'" '+selected+' />';
				}
				
				return E.html.createDiv(false, 'input radio', false, html);
			},
			
			
			/**
			 * Creates Buttons
			 *
			 * @param string prefix - the options prefix.
			 * @param string fields - the fields ids.
			 * @param string options - the buttons titles.
			 * @return string
			 */
			createButtons: function(prefix, fields, options) {
				var html = '';
				for(var option in options) {
					html+= '<button id="'+fields[option]+'" title="'+options[option]+'" class="silver '+prefix+'-'+option+'"><div class="icon-small"></div></button>';
				}
				
				return html;
			},
			
			
			/**
			 * Creates a Text Area Field
			 *
			 * @param string id - the field id.
			 * @param string label - the label text content (optional).
			 * @param string value - the field value.
			 * @param string max - the maximum text length (optional).
			 * @return string
			 */
			createTextArea: function(id, label, value, max) {
				label = label? this.createLabel(id, label) : '';
				max = max? 'maxlength="'+max+'"' : '';
				
				return 	'<div class="input textarea required">'+
							label+
							'<textarea id="'+id+'" cols="30" rows="3" '+max+'>'+value+'</textarea>'+
						'</div>';
			},
			
			
			/**
			 * Creates a Fieldset
			 *
			 * @param string id - the fieldset id.
			 * @param string legend - the fieldset name.
			 * @param string contents - the fieldset html content.
			 */
			createFieldset: function(id, legend, contents) {
				if(contents.length > 0) {
					var fieldset =
						'<fieldset id="'+id+'">'+
							'<legend>'+legend+'</legend>'+
								contents+
						'</fieldset>';
					return fieldset;
				}
				else return contents;
			}
		}
	};
	
});