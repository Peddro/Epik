var currentProject, currentGame;

$(document).ready(function() {
	
	// Check Support
	if($.browser.opera) {
		$(window).resize();
		E.sections.get().children('.'+classes.list).removeClass('ajax');
		alert(E.strings.alerts.noSupport);
		return false;
	}
	
	// Project Current Values
	E.project.current = {
		tool: $(),
		menu: $(),
		scenario: $(), // The current scenario displayed
		element: $(), // The current element selected
		clipboard: {}, // The current cut or copied element
		rightClicked: $(), // The current right clicked element
		saving: false
	};
	
	// Current Values
	currentProject = E.project.current;
	currentGame = E.game.current;
	
	// Page Elements Ids
	ids.toolbar = 'toolbar';
	ids.menus = 'menus';
	ids.explorer = 'explorer';
	ids.properties = 'properties';
	ids.canvas = 'canvas';
	
	// Elements Fields Ids
	ids.fields = {
		actions: {
			general: 'field-actions'
		},
		background: {
			general: 'field-background',
			alpha: 'field-background-alpha',
			color: 'field-background-color'
		},
		border: {
			general: 'field-border',
			thickness: 'field-border-thickness',
			alpha: 'field-border-alpha',
			color: 'field-border-color'
		},
		content: 'field-content',
		dimensions: {
			general: 'field-dimensions',
			length: 'field-dimensions-length',
			height: 'field-dimensions-height',
			radius: 'field-dimensions-radius',
			thickness: 'field-dimensions-thickness',
			width: 'field-dimensions-width'
		},
		flow: {
			general: 'field-jumps',
			start: 'field-jumps-start',
			tos: {},
			ons: { timeout: 'field-jumps-timeout-on' }
		},
		font: {
			general: 'field-font',
			color: 'field-font-color',
			size: 'field-font-size',
			style: 'field-font-style'
		},
		helps: { general: 'field-helps' },
		sync : { 
			general: 'field-sync',
			div: 'field-sync-div',
			checkbox: 'field-sync-checkbox',
		},
		name : 'field-name',
		playersNumber: 'field-players-number',
		position: {
			general: 'field-position',
			type: 'field-position-type',
			absolute: {
				general: 'field-position-absolute',
				x: 'field-position-absolute-point-x',
				y: 'field-position-absolute-point-y'
			},
			aligned: {
				general: 'field-position-aligned',
				horizontal: 'field-position-aligned-horizontal',
				vertical: 'field-position-aligned-vertical',
			},
			corner: 'field-position-corner',
			side: 'field-position-side',
			z: {
				general: 'field-z',
				bringToFront: 'field-z-bringToFront',
				sendToBack: 'field-z-sendToBack',
				bringForward: 'field-z-bringForward',
				sendBackward: 'field-z-sendBackward'
			}
		},
		rules: {
			general: 'field-rules',
			helps: 'field-rules-helps',
			bonus: { general: 'field-rules-bonus' }
		},
		scores: {
			general: 'field-scores',
			helps: {
				general: 'field-scores-helps',
				name: 'field-scores-helps-name',
				value: 'field-scores-helps-value',
				log: 'field-scores-helps-log'
			}
		},
		sounds: 'field-sounds',
		tail: {
			general: 'field-tail'
		},
		transformations: {
			general: 'field-transformations',
			rotation: 'field-transformations-rotation'
		}
	}
	
	// Bonus Fields
	var scenariosBonus = E.defaults.types.bonus, bonusFields = ids.fields.rules.bonus;
	for(var scenarioType in scenariosBonus) {
		for(var i = 0; i < scenariosBonus[scenarioType].length; i++) {
			var bonusType = scenariosBonus[scenarioType][i];
			bonusFields[bonusType] = {
				value: bonusFields.general+'-'+bonusType+'-value',
				log: bonusFields.general+'-'+bonusType+'-log'
			};
		}
	}
	
	// Jump Fields
	var scenariosJumps = E.defaults.types.jumps, flowFields = ids.fields.flow;
	for(var scenarioType in scenariosJumps) {
		for(var i = 0; i < scenariosJumps[scenarioType].length; i++) {
			var jumpType = scenariosJumps[scenarioType][i];
			flowFields.tos[jumpType] = flowFields.general+'-'+jumpType+'-to';
		}
	}
	
	// Helps Fields
	var helpsTypes = E.defaults.helps.all, helpsFields = ids.fields.helps;
	for(var helpName in helpsTypes) {
		helpsFields[helpName] = {
			use : helpsFields.general+'-'+helpName+'-use',
			selected : helpsFields.general+'-'+helpName+'-selected'
		};
	}
	delete E.defaults.helps.all;
	
	// Scores Fields
	var scoresTypes = E.defaults.types.scores, scoresFields = ids.fields.scores, activitiesScores = E.defaults.scores;
	for(var i = 0; i < scoresTypes.length; i++) {
		var generalName = scoresFields.general+'-'+scoresTypes[i];
		scoresFields[scoresTypes[i]] = {
			general: generalName,
			name: generalName+'-name',
			log: generalName+'-log'
		};
		
		if(scoresTypes[i] in activitiesScores) {
			scoresFields[scoresTypes[i]].value = generalName+'-value';
		}
	}
	if(typeof activitiesScores.timeout !== 'undefined') {
		scoresFields.timeout = {
			general: 'field-scores-timeout',
			value: 'field-scores-timeout-value'
		};
	}
	
	
	// Page Elements Classes
	classes.elementMenu = 'element-menu';
	classes.icons = E.defaults.icons;
	delete E.defaults.icons;
	
	// Tools Events
	E.tools.events();
	
	// Menus Events
	E.menus.events();
	
	// Sections Events
	E.sections.generalEvents();
	
	// Load Project
	$(window).resize();
	E.load.project();
	
});


/**
 * Event to hide menus when document is clicked.
 */
$(document).click(function(event) {
	currentProject.tool.click();
	$('#'+ids.explorer+' > .'+classes.elementMenu).hide();
});


/**
 * Event to avoid window close if there is unsaved progress.
 */
$(window).bind('beforeunload', function(event) {
	if(typeof E.project.save != 'undefined') {
		return E.strings.alerts.close;
	}
});