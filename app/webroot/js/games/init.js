var ids, classes;

$(document).ready(function() {
	
	var body = $('body');
	var game = E.game;
	
	ids = E.selectors.ids;
	classes = E.selectors.classes;
	
	// Browsers - Adds a class to body that specifies the browser type.
	if($.browser.chrome) {
		body.addClass('chrome');
	}
	else if($.browser.safari) {
		body.addClass('safari');
	}
	else if($.browser.opera) {
		body.addClass('opera');
	}
	else if($.browser.msie) {
		body.addClass('msie');
	}
	else if($.browser.mozilla) {
		body.addClass('moz');
	}
	
	// Initialize the Game
	game.init();
	
	// Set App Logo and Time Manager
	game.logo = new game.Logo(E.tmp.data.properties.logo.styles);
	game.timeManager = new game.TimeManager();
	
	// Check Support
	if($.browser.opera) {
		
		// Set Game Over Scenario as Current
		game.setScenario(null, E.defaults.screens.gameover);
		
		// Resize Window
		$(window).resize();
		
		alert(E.strings.alerts.noSupport);
		return false;
	}
	else {
		
		// Set Start Scenario as Current
		game.setScenario(null, E.defaults.screens.start);

		// Resize Window
		$(window).resize();
	}
});


/**
 * Triggered on window resize.
 */
$(window).resize(function() {
	E.game.utils.setScreenTopMargin();
});


/**
 * Event to avoid window close.
 */
$(window).bind('beforeunload', function(event) {
	var game = E.game;
	if(game.current.scenario && game.server.socket) {
		return E.strings.alerts.close;
	}
});
