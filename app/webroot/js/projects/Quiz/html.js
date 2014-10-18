	$(document).ready(function() {

	/**
	 * Creates an Element Properties Form
	 *
	 * This method creates the form for the given element to be displayed on the properties panel on the right.
	 * The form is divided into sections, created in the order the following order (which is also the display order):
	 * 		- General Fields: name, text content (for text elements), hints and resources (for questions), 
	 * 		  players number (for players property), and sounds buttons (for sounds property);
	 *		- Font Fields: font size, style, and color;
	 *		- Dimensions Fields: width, height, length, radius;
	 * 		- Position Fields: absolute, aligned, corner (for logo property), and side (for players property);
	 * 		- Border Fields: thickness and color;
	 * 		- Background Fields: color;
	 * 		- Scores and Helps Fields: score/helps name, value (for questions), and log;
	 * 		- Rules and Jumps Fields: scenario helps, bonus, jumps to and on fields (just for scenarios);
	 * 		- Transformation Fields: rotation;
	 * 		- Tail Fields: corners (just for balloons and questions);
	 * 		- Actions: view, edit, manage, and choose buttons (just for resources and activities).
	 *
	 * @param string element - the element object.
	 * @param string position - the element position.
	 * @return string
	 */
	E.html.createPropertiesForm = function(element, position) {
		var self = this,
		html = '',
		defaults = E.defaults,
		collections = defaults.collections,
		fields = ids.fields,
		strings = E.strings.labels,
		forms = this.form,
		sections = {};
		
		var minimums = defaults.minimum, maximums = defaults.maximum, units = E.strings.units;
		
		var icon = element.getIcon(),
			notProperty = element.parent != 'property',
			notScenario = !(element instanceof E.game.Scenario),
			notResource = !(element instanceof E.game.Audio || element instanceof E.game.Image || element instanceof E.game.Video || element instanceof E.game.PDF),
			isActivity = (element instanceof E.game.Question),
			id = notProperty? element.getId() : icon;
		
		// Create General Fields
		if(notProperty) {
			
			// Name Field
			html+= forms.createTextInput(fields.name, strings.name, false, true, element.getName(), maximums.name);
			
			// Content Field
			if(element instanceof E.game.Heading) {
				html+= forms.createTextInput(fields.content, strings.content, false, true, element.getText(), maximums.text);
			}
			else if(element instanceof E.game.Paragraph || element instanceof E.game.Square || element instanceof E.game.Circle || element instanceof E.game.Balloon) {
				html+= forms.createTextArea(fields.content, strings.content, element.getText(), maximums.paragraph);
			}
		}
		else if(element instanceof E.game.Players) {
			
			// Number of Players Field
			html+= forms.createNumberInput(fields.playersNumber, strings.playersNumber, false, true, element.getPlayersNumber(), minimums.players, maximums.players);
		}
		else if(element instanceof E.game.Sounds) {
			
			var list = element.elements;
			if(typeof list != 'undefined') {
				
				for(var i = 0; i < list.length; i++) {
					html+= this.createDiv(false, false, false,
						forms.createLabel(fields[icon]+'-'+list[i], strings[icon][list[i]], strings[icon][list[i]+'Desc'])+
						this.createIcon('remove', strings.nosound)+
						this.createModalButtonLink(strings.choose, false, E.system.server + collections[3] + '/choose_type/' + defaults.genre + '/1')
					);
				}
			}	
		}
		
		
		// Create Font Section
		if(notProperty && typeof element.text != 'undefined') {
			var fontFields = fields.font;
			sections.font = 
				forms.createSelect(fontFields.size, false, false, true, element.getFontSize(), defaults.fonts.sizes)+
				forms.createSelect(fontFields.style, false, false, true, element.getFontStyle(), defaults.fonts.styles)+
				forms.createColorInput(fontFields.color, false, false, true, element.getFontColor(), false);
		}
		
		
		// Create Dimensions Section
		if(notProperty && notScenario) {
			var dimensionsFields = fields.dimensions;
			switch(true) {
				case element instanceof E.game.Circle:
					sections.dimensions = forms.createNumberInput(dimensionsFields.radius, strings.radius, false, true, element.getRadius(), 0, maximums.numbers);
					break;
				
				case element instanceof E.game.Line:
					sections.dimensions = forms.createNumberInput(dimensionsFields.length, strings.length, false, true, element.getWidth(), 0, maximums.numbers);
					break;
					
				case element instanceof E.game.Question:
					sections.dimensions = forms.createNumberInput(dimensionsFields.width, strings.width, false, true, element.getWidth(), 0, maximums.numbers);
					break;
				
				default:
					sections.dimensions =
						forms.createNumberInput(dimensionsFields.width, strings.width, false, true, element.getWidth(), 0, maximums.numbers)+
						forms.createNumberInput(dimensionsFields.height, strings.height, false, true, element.getHeight(), 0, maximums.numbers);
					break;
			}
		}
		
		
		// Create Position Section
		if(position) {
			var absoluteFields = fields.position.absolute;
			var alignedFields = fields.position.aligned;
			var zFields = fields.position.z;
			
			var selected = null, absolute = { point: {x: 0, y: 0}, classes: '' }, aligned = { horizontal: 'left', vertical: 'middle', classes: '' };
				
			if(typeof position.absolute != 'undefined') {
				selected = 'absolute';
				absolute.classes = classes.selected;
				absolute.point = position.absolute.point;
			}
			else {
				selected = 'aligned';
				aligned.classes = classes.selected;
				aligned.horizontal = position.aligned.horizontal;
				aligned.vertical = position.aligned.vertical;
			}
			sections.position = 
				forms.createSelect(fields.position.type, strings.type, false, true, selected, defaults.positions)+
				this.createDiv(absoluteFields.general, absolute.classes, false,
					forms.createNumberInput(absoluteFields.x, strings.x, false, true, absolute.point.x, 0, maximums.numbers)+
					forms.createNumberInput(absoluteFields.y, strings.y, false, true, absolute.point.y, 0, maximums.numbers)
				)+
				this.createDiv(alignedFields.general, aligned.classes, false,
					forms.createRadioButtons(alignedFields.horizontal, false, 'align', aligned.horizontal, defaults.aligned.horizontal)+
					forms.createRadioButtons(alignedFields.vertical, false, 'align', aligned.vertical, defaults.aligned.vertical)
				)+
				this.createDiv(zFields.general, false, false, forms.createButtons('z', zFields, strings.z));
		}
		else if(element instanceof E.game.Logo) {
			sections.position = this.createDiv(fields.position.corner, false, false, forms.createRadioButtons(fields.position.corner, false, 'corner', element.getCorner(), defaults.corners));
		}
		else if(element instanceof E.game.Players) {
			sections.position = this.createDiv(fields.position.side, false, false, forms.createRadioButtons(fields.position.side, false, 'side', element.getSide(), defaults.sides));
			sections.position+= this.createParagraph(strings.playersPositionDesc);
		}
		
		
		// Create Border Section
		if((notProperty && notScenario) || element instanceof E.game.Players) {
			var borderFields = fields.border;
			var nocolor = element.getBorderColor() == 'transparent';
			sections.border = 
				forms.createNumberInput(borderFields.thickness, strings.thickness, false, true, element.getBorderThickness(), 0, maximums.thickness)+
				forms.createCheckbox(borderFields.alpha, strings.color.general, strings.nobcolor, nocolor, true, true)+
				forms.createColorInput(borderFields.color, false, false, true, element.getBorderColor(), nocolor);
		}
		
		
		// Create Background Section
		if(notResource && ((notProperty && !(element instanceof E.game.Line)) || element instanceof E.game.Players)) {
			var backgroundFields = fields.background;
			var createBackground = function(suffix) {
				var html = '';
				var value = element.getBackgroundColor(suffix);
				
				if(value) {
					var alpha = suffix? backgroundFields.alpha+'-'+suffix : backgroundFields.alpha;
					var color = suffix? backgroundFields.color+'-'+suffix : backgroundFields.color;
					var label = suffix? strings.color[suffix] : strings.color.general;
					var nocolor = value == 'transparent';
					
					if(typeof value != 'undefined') {
						html =
							forms.createCheckbox(alpha, label, strings.nobgcolor, nocolor, true, true)+
							forms.createColorInput(color, false, false, true, value, nocolor);
					}
					return html;
				}
				else return false;
			}
			
			var background = element.styles.background;
			if(typeof background == 'object') {
				sections.background = '';
				for(var key in background) {
					sections.background+= this.createDiv(false, false, false, createBackground(key));
				}
			}
			else sections.background = createBackground();
		}
		
		
		// Create Scores and Helps Sections
		if(isActivity && element.source) {
			var scores = element.scores, rewardFields = fields.scores.reward, penaltyFields = fields.scores.penalty, timeoutFields = fields.scores.timeout;
			var helps = element.helps, helpsFields = fields.helps;

			sections.scores =
				this.createDiv(rewardFields.general, false, false,
					forms.createNumberInput(rewardFields.value, strings.reward, false, true, scores.reward.value, 0, maximums.numbers)+
					forms.createCheckbox(rewardFields.log, strings.log, strings.logQuestionRewardDesc, scores.reward.log)
				)+
				this.createDiv(penaltyFields.general, false, false,
					forms.createNumberInput(penaltyFields.value, strings.penalty, false, true, scores.penalty.value, 0, maximums.percentage, units.penalty)+
					forms.createCheckbox(penaltyFields.log, strings.log, strings.logQuestionPenaltyDesc, scores.penalty.log)
				);
			
			sections.helps = '';
			$.each(helps, function(i, helpData) {
				var hasList = typeof helpData.all != 'undefined', 
					isDisabled = !element.isGroup() && hasList && !helpData.all,
					selectMultiple = i == 'hints';
				
				var helpsHtml = forms.createCheckbox(helpsFields[i].use, strings.help[i], strings.help[i+'Desc'], helpData.use, false, false, isDisabled);
				if(hasList && helpData.all) {
					helpsHtml+= forms.createSelect(helpsFields[i].selected, strings[i], strings[i+'Desc'], false, helpData.selected, helpData.all, selectMultiple);
				}
				
				sections.helps+= self.createDiv(null, null, null, helpsHtml);
			});

			if(typeof scores.timeout !== 'undefined') {
				sections.helps+= forms.createNumberInput(timeoutFields.value, strings.ctimeout, strings.ctimeoutDesc, true, scores.timeout.value, minimums.timeout, maximums.timeout, units.timeout);
			}
		}
		else if(element instanceof E.game.Scores) {
			var scoresFields = fields.scores, helpsFields = fields.scores.helps;
			sections.scores = '';
			
			var teamScore = element.getScoreOfType('team');
			if(typeof teamScore != 'undefined') {
				sections.scores =
					this.createDiv(scoresFields.team.general, false, false,
						forms.createTextInput(scoresFields.team.name, strings.team, false, true, teamScore.name, maximums.text)+
						forms.createCheckbox(scoresFields.team.log, strings.log, strings.logTeamScoreDesc, teamScore.log)
					);
			}
			
			var playersScores = element.getPlayersScores();
			$.each(playersScores, function(type) {
				sections.scores+=
					self.createDiv(scoresFields[type].general, false, false,
						forms.createTextInput(scoresFields[type].name, strings[type], false, true, playersScores[type].name, maximums.text)+
						forms.createCheckbox(scoresFields[type].log, strings.log, strings.logGlobalDesc, playersScores[type].log)
					);
			});
			
			var helps = element.getScoreOfType('helps');
			sections.helps =
				this.createDiv(scoresFields.helps.general, false, false,
					forms.createTextInput(helpsFields.name, false, false, true, helps.name, maximums.text)+
					forms.createNumberInput(helpsFields.value, false, false, true, helps.value, minimums.helps, maximums.helps)+
					forms.createCheckbox(helpsFields.log, strings.log, strings.logHelpsDesc, helps.log)
				);

			//Create sync Section
			var sync = element.sync, syncFields = fields.sync;
			sections.sync = this.createDiv(syncFields.div, null, null, forms.createCheckbox(syncFields.checkbox, strings.sync, null, true));
		}
		
		
		// Create Rules and Jumps Section
		if(element instanceof E.game.Scenario) {
			var game = E.game;
			var rulesFields = fields.rules, flowFields = fields.flow;
			
			if(typeof element.rules != 'undefined' && element.rules) {
				
				// Helps
				if(element.type == defaults.types[collections[0]][1]) {
					sections.rules = forms.createNumberInput(rulesFields.helps, strings.helps, strings.scenarioHelpsDesc, false, element.getHelps(), 0, game.scores.getScoreOfType('helps').value);
				}
				
				// Bonus
				var bonusTypes = defaults.types.bonus[element.type];
				if(bonusTypes.length > 0) {
					var bonusFields = rulesFields.bonus;
					for(var i = 0; i < bonusTypes.length; i++) {
						var bonus = bonusTypes[i];
						
						sections.rules+= 
							this.createDiv(false, bonusFields.general, false,
								forms.createNumberInput(bonusFields[bonus].value, strings.bonus[bonus], false, false, element.getBonus(bonus), 0, maximums.numbers)+
								forms.createCheckbox(bonusFields[bonus].log, strings.log, strings.logGlobalDesc, element.getBonusLog(bonus))
							);
					}
				}
				
			}
			
			// Game Start
			sections.flow = forms.createCheckbox(flowFields.start, strings.gameStart, strings.gameStartDesc, (game.start == id));
			
			// Jumps
			var scenarios = game.scenarios, scenariosList = { 0 : strings.none };
			
			// Create Jumps List
			for(var i = 0; i < scenarios.length; i++) {
				var scenario = scenarios[i], currentId = scenario.getId();
				if(currentId != id && currentId != game.start) {
					scenariosList[currentId] = scenario.getName();
				}
			}
			if(id != game.start) {
				scenariosList[1] = strings.gameEnd;
			}
			
			// Create Jumps Selects
			var jumpsFields = flowFields.tos, jumpsTypes = defaults.types.jumps[element.type];
			for(var j = 0; j < jumpsTypes.length; j++) {
				var jump = jumpsTypes[j];
				var data = element.getJump(jump), extra = false;
				var jumpTo = data? ((typeof data.to != 'undefined')? data.to : 1) : 0;
				
				if(typeof flowFields.ons[jump] != 'undefined') {
					extra = forms.createNumberInput(flowFields.ons[jump], false, false, false, (data && 'on' in data)? data.on : 0, minimums.timeout, maximums.timeout, units.timeout);
				}
				sections.flow+= forms.createSelect(jumpsFields[jump], strings.jump[jump], strings.jump[jump+'Desc'], false, jumpTo, scenariosList, false, extra);
			}
			
			sections.flow+= this.createParagraph(strings.gameEndDesc);
		}
		
		
		// Create Transformations Section
		if(notProperty && notScenario && !(element instanceof E.game.Circle)) {
			sections.transformations = forms.createNumberInput(fields.transformations.rotation, strings.rotation, false, true, element.getRotation(), 0, maximums.rotation, units.rotation);
		}
		
		
		// Create Tail Section
		if(element instanceof E.game.Balloon || isActivity) {
			sections.tail = forms.createRadioButtons(fields.tail.general, false, 'tail', element.getTail(), defaults.corners);
		}
		
		
		// Create Actions Section
		if(!notResource || isActivity) {
			var controller = isActivity? collections[4] : collections[3], source = element.getSource();
			
			if(source) {
				var mainURL = E.system.server + controller;
				sections.actions = 
					this.createModalIconLink(strings.view, 'view', mainURL + '/view/' + source)+
					this.createModalIconLink(strings.edit, 'edit', mainURL + '/edit/' + source);

				if(element instanceof E.game.Question && !element.isGroup()) {
					sections.actions+= 
						this.createModalIconLink(strings.associateHints, 'hint', mainURL + '/hints/' + source)+
						this.createModalIconLink(strings.associateResources, 'resource', mainURL + '/resources/' + source);
				}

				sections.actions+= this.createParagraph(strings.actionsDesc);
			}
			else {
				
				if(element instanceof E.game.Question) {
					var tool = $('#'+ids.toolbar+' .tool.'+classes.icons.question+' a');
					sections.actions = this.createModalButtonLink(strings.choose + ' ' + strings.question, classes.icons.question, tool.attr('href'));
					
					tool = $('#'+ids.toolbar+' .tool.'+classes.icons.group+' a');
					sections.actions+= this.createModalButtonLink(strings.choose + ' ' + strings.group, classes.icons.group, tool.attr('href'));
				}
				else {
					var tool = $('#'+ids.toolbar+' .tool.'+icon+' a');
					sections.actions = this.createModalButtonLink(strings.choose, element.getIcon(), tool.attr('href'));
				}
			}
		}
		
		
		// Put each section inside a fieldset
		for(var section in sections) {
			html+= forms.createFieldset(fields[section].general, strings[section], sections[section]);
		}
		
		return '<form action="'+id+'">'+html+'</form>';
	}
	
});