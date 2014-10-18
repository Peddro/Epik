/*
 * Triggered when document finishes loading.
 */
$(document).ready(function() {
	
	
	/* PLAYERS FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Players.prototype = {
		
		createDOM : function() {
			this.dom = $('#players');
			
			var scores = E.game.scores, list = this.dom.children('.list'), avatarRows = (E.defaults.types.scores.length + 1);

			var createNameValue = function(name, value) {
				return '<span class="name">'+name+'</span><span class="dots">:</span><span class="value">'+value+'</span>';
			};

			// Team Score
			if(this.info.length > 1) {
				var teamScore = scores.getScoreOfType('team');
				if(teamScore) {
					list.prepend($('<div class="team">'+createNameValue(teamScore.name, 0)+'</div>'));
					avatarRows--;
				}
			}

			// Players
			var playersList = list.children('ul');
			var playerClasses = 'player';
			for(var i = 0; i < this.info.length; i++) {
				var classes = (i == 0)? playerClasses + ' ' + this.elements[1] : playerClasses + ' ' + this.elements[2];
				
				var item = $('<li id="'+this.info[i].id+'" class="'+classes+'"><table><tr><td class="avatar" rowspan="'+ avatarRows +'"></td><td><div class="player-name">'+this.info[i].name+'</div></td></tr></table></li>');
				
				// Player Avatar
				var image = this.info[i].image;
				image = (image instanceof Image)? image : $('<img src="'+image+'" />');
				item.find('.avatar').append(image);

				// Player Scores
				var table = item.children('table');
				var playersScores = scores.getPlayersScores();
				for(var score in playersScores) {
					table.append($('<tr><td class="score '+score+'">'+createNameValue(playersScores[score].name, 0)+'</td></tr>'));
				}
				
				// Player Jump Status
				table.append($('<tr><td class="status" colspan="2"></td></tr>'));

				playersList.append(item);
			}

			// Helps
			var helps = scores.getScoreOfType('helps');
			list.append($('<div class="helps">'+createNameValue(helps.name, helps.value)+'</div>'));

			// Apply Styles
			var backgrounds = this.styles.background;
			this.setSide(this.styles.side);
			this.setBorderThickness(this.styles.border.thickness);
			this.setBorderColor(this.styles.border.color);
			for(var key in backgrounds) {
				this.setBackgroundColor(backgrounds[key].color, key);
			}
		},
		
		show : function() {
			if(this.dom && !this.dom.is(':visible')) {
				this.dom.fadeIn('fast');
			}
		},
		
		hide : function() {
			if(this.dom && this.dom.is(':visible')) {
				this.dom.fadeOut('fast');
			}
		},
		
		resize : function() {
			var side = this.getSide();
			var hidden = !this.isVisible();
			
			if(hidden) {
				this.dom.css({ visibility : 'hidden', display : 'block' });
			}
			
			var stage = E.game.stage, layer = E.game.layers.elements, x, y, w, h;
			var players = this.dom.find('.player:visible'), ul = this.dom.children('.list').children('ul');
			
			if(this.isVertical()) {
				var totalHeight = this.dom.children('.list').height();
				
				// Set ul Height
				var ulHeight = totalHeight - (parseInt(ul.css('marginTop')) + parseInt(ul.css('marginBottom')));
				ul.height(ulHeight);
				
				// Calculate Margin
				var margin = 0;
				players.each(function(key, value) { margin+= $(value).outerHeight() });
				margin = (ulHeight - margin)/players.length;
				
				// Apply Margin
				players.css({ marginLeft: '', marginRight: '', marginBottom: margin });
				players.first().css('marginTop', margin/2);
				
				// Calculate Elements Layer Position and Size
				x = (this.isLeft())? this.dom.outerWidth() : 0; y = 0;
				w = stage.getWidth() - this.dom.outerWidth();
				h = stage.getHeight();
			}
			else {
				
				// Set ul Height
				var ulWidth = this.dom.children('.list').children('ul').width();
				ul.css('height', '');
				
				// Calculate Margin
				var margin = 0;
				players.each(function(key, value) { margin+= $(value).outerWidth() });
				margin = Math.floor(((ulWidth - margin)/players.length)/2);
				
				// Apply Margin
				players.css({ marginTop: '', marginLeft: margin, marginRight: margin, marginBottom: '' });
				
				// Calculate Elements Layer Position and Size
				x = 0; y = (this.isTop())? this.dom.outerHeight() : 0;
				w = stage.getWidth();
				h = stage.getHeight() - this.dom.outerHeight();
			}
			
			if(hidden) {
				this.dom.css({ visibility : '', display : '' });
			}
			
			// Set Elements Layer Position and Size
			layer.setSize(w, h);
			layer.setPosition(x, y);
		},
		
		
		// Flags
		isTop : function() {
			return this.getSide() == 'top';
		},
		
		isBottom : function() {
			return this.getSide() == 'bottom';
		},
		
		isLeft : function() {
			return this.getSide() == 'left';
		},
		
		isRight : function() {
			return this.getSide() == 'right';
		},
		
		isHorizontal : function() {
			return (this.isTop() || this.isBottom());
		},
		
		isVertical : function() {
			return (this.isLeft() || this.isRight());
		},
		
		isVisible : function() {
			return this.dom.is(':visible');
		},
		
		
		// Getters for General Styles
		getPlayersNumber : function() {
			return this.max;
		},
		
		getCurrentPlayer : function() {
			return this.info[0];
		},
		
		getSide : function() {
			return this.styles.side;
		},
		
		getInverseSide : function() {
			switch(this.getSide()) {
				case 'top': return 'bottom';
				case 'left': return 'right';
				case 'right': return 'left';
				case 'bottom': return 'top';
			}
		},
		
		getBorderThickness : function() {
			return this.styles.border.thickness;
		},
		
		getBorderColor : function() {
			return this.styles.border.color;
		},
		
		getBackgroundColor : function(element) {
			if(element in this.styles.background) {
				return this.styles.background[element].color;
			}
			return null;
		},
		
		
		// Setters for General Styles
		setPlayersNumber : function(number) {
			this.max = number;
			if(this.dom) {
				this.dom.find('.player').hide().slice(0, number).show();
				this.addStatus();
				this.resize();
			}
		},
		
		setInfo : function(info) {
			this.info = info;
		},
		
		setSide : function(side) {
			
			// Reset Styles
			var border = this.getBorderThickness();
			this.setBorderThickness(0);
			
			// Reset Rankings Screen Side
			var rankingsScreen = this.dom.parent().children('.'+E.defaults.screens.rankings).removeClass(this.getInverseSide());
			
			// Set Players Side
			this.styles.side = side;
			this.dom.attr('class', side);
			this.setBorderThickness(border);
			this.resize();
			
			// Set Rankings Screen Side
			rankingsScreen.addClass(this.getInverseSide());
		},
		
		setScoreName : function(value, type) {
			if(typeof this.dom != 'undefined') {
				this.dom.find('.'+type).children('.name').text(value);
			}
		},
		
		setScoreValue : function(value, type, id) {
			if(typeof this.dom != 'undefined') {
				var selector = id? ('#' + id + ' .' + type) : ('.' + type);
				this.dom.find(selector).children('.value').text(value);
			}
		},
		
		setNormal : function(id) {
			var status = this.elements[2];
			if(typeof this.dom != 'undefined') {
				this.dom.find('#'+id).removeClass(this.elements[3]).addClass(status);
				this.setBackgroundColor(this.styles.background[status].color, status);
				this.removeStatus(id, this.status[0]);
			}
		},
		
		setCollaborating : function(id, type) {
			var status = this.elements[3];
			if(typeof this.dom != 'undefined') {
				this.dom.find('#'+id).removeClass(this.elements[2]).addClass(status);
				this.setBackgroundColor(this.styles.background[status].color, status);
				this.addStatus(id, type);
			}
		},
		
		addStatus : function(id, type) {
			if(!type) type = 'default';
			var html = E.strings.labels['playerStatus'+type.ucfirst()]+'</span>';
			var selector = id? '#'+id : '';
			var statusDOM = this.dom.find(selector + ' .status');
			
			
			if(statusDOM.length > 0) {
				if(type == 'default') {
					statusDOM.html('<span>'+html);
				}
				else {
					var game = E.game, currentScenario = game.current.scenario;
					statusDOM.children(':not(.'+this.status[0]+', .'+this.status[1]+')').remove();
					if(statusDOM.children().length > 0) html = ', '+html;
					
					if(type == 'help' || type == 'helping') {
						statusDOM.append('<span class="'+this.status[0]+'">'+html);
					}
					else if(currentScenario) {
						var jumpsTypes = E.defaults.types.jumps[currentScenario.type];

						if(jumpsTypes.indexOf(type) >= 0) {
							statusDOM.append('<span class="'+this.status[1]+'">'+html);
						}
					}
				}
			}
		},
		
		removeStatus : function(id, type) {
			var selector = id? '#'+id : '';
			var statusDOM = this.dom.find(selector + ' .status');
			statusDOM.children('.'+type).remove();
			
			if(statusDOM.children().length == 0) {
				this.addStatus();
			}
		},
		
		setLimit : function(value, type) {
			var scoreDOM = this.dom.find('.'+type), limitDOM = scoreDOM.children('.limit');
			
			if(typeof value == 'number' && value >= 0) {
				value+= '/';
				
				if(limitDOM.length > 0) {
					limitDOM.text(value);
				}
				else scoreDOM.children('.value').before('<span class="limit">'+value+'</span>');
			}
			else if(limitDOM.length > 0) {
				limitDOM.remove();
			}
		},
		
		setBorderThickness : function(width) {
			this.styles.border.thickness = width;
			this.dom.children('.list').css('border-'+this.getInverseSide()+'-width', width);
		},
		
		setBorderColor : function(color) {
			this.styles.border.color = color;
			this.dom.children('.list').css('border-color', color);
		},
		
		setBackgroundColor : function(color, element) {
			this.styles.background[element].color = color;
			if(element == this.elements[0]) {
				this.dom.css('background-color', color);
			}
			else {
				this.dom.find('.player.'+element).css('background-color', color);
			}
		},
		
		removePlayer : function(id) {
			if(typeof id != 'undefined') {
				var self = this;
				
				this.dom.find('#'+id).fadeOut('fast', function() {
					$(this).remove();
					self.resize();
				});
			}
		}
		
	};
	$.extend(E.game.Players.prototype, E.game.Property.prototype);
	
	
	/* SCORES FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	E.game.Scores.prototype = {
		
		setTeamScore : function(name, log) {
			this.team.name = name;
			this.team.log = log;
		},
		
		addPlayerScore : function(type, name, log) {
			this.players[type] = { name: name, log: log };
		},
		
		setHelps : function(name, value, log) {
			this.helps.name = name;
			this.helps.value = value;
			this.helps.log = log;
		},
		
		setName : function(value, type) {
			var score = this.getScoreOfType(type);
			
			if(typeof score != 'undefined') {
				score.name = value;
				
				if(typeof E.game.players != 'undefined') {
					E.game.players.setScoreName(value, type);
				}
			}
		},
		
		setValue : function(value, type) {
			var score = this.getScoreOfType(type);
			
			if(typeof score != 'undefined' && value != score.value) {
				score.value = value;
				
				if(typeof E.game.players != 'undefined') {
					E.game.players.setScoreValue(value, type);
				}
				
				if(typeof score.limit != 'undefined') {
					this.setLimit(score.limit - 1, type);
				}
			}
		},
		
		setLimit : function(value, type) {
			var score = this.getScoreOfType(type);
			
			if(typeof score != 'undefined') {
				if(typeof value === 'number' && value >= 0) {
					if(value < score.value) {
						score.limit = value;
					}
					else {
						value = null;
					}
				}
				else {
					value = null;
				}
				
				if(value === null && typeof score.limit != 'undefined') {
					delete score.limit;
				}
				
				E.game.players.setLimit(value, type);
			}
		},
		
		setLog : function(value, type) {
			this.getScoreOfType(type).log = value;
		},
		
		getPlayersScores : function() {
			return this.players;
		},
		
		getHelps : function(limited) {
			return (limited && typeof this.helps.limit != 'undefined')? this.helps.limit : this.helps.value;
		},
		
		getScoreOfType : function(type) {
			switch(type) {
				case 'team': case 'helps':
					return this[type];
					break;
					
				default:
					return this.players[type];
					break;
			}
		}
	};
	$.extend(E.game.Scores.prototype, E.game.Property.prototype);
	
	
	/* SOUNDS FUNCTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	var playSound = function(file) {
		if(file && file.readyState > 1) {
			file.currentTime = 0;
			file.play();
		}
	}
	
	E.game.Sounds.prototype = {
		
		getSourceId : function(sound) {
			return this[sound].id;
		},
		
		setSourceId : function(id, sound) {
			this[sound].id = id;
			if(!id) this.setFile(E.defaults.resources[sound].object, sound);
		},
		
		getFile : function(sound) {
			return this[sound].file;
		},
		
		setFile : function(file, sound) {
			this[sound].file = file;
		},
		
		playBackground : function() {
			var file = this[this.elements[0]].file;
			if(file) {
				this.setBackgroundVolume();
				file.loop = true;
				playSound(file);
			}
		},
		
		stopBackground : function() {
			var file = this[this.elements[0]].file;
			if(file) file.pause();
		},
		
		getBackgroundVolume : function() {
			var file = this[this.elements[0]].file;
			if(file) return file.volume;
		},
		
		setBackgroundVolume : function(low) {
			var file = this[this.elements[0]].file;
			if(file) file.volume = low? 0.2 : 0.5;
		},
		
		playCorrect : function() {
			playSound(this[this.elements[1]].file);
		},
		
		playIncorrect : function() {
			playSound(this[this.elements[2]].file);
		},
		
		playHelp : function() {
			playSound(this[this.elements[3]].file);
		}
		
	};
	$.extend(E.game.Sounds.prototype, E.game.Property.prototype);
		
});
