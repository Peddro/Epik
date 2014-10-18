var list = {};
var genreList = {};

exports.Quiz = function(gameId, id, isCollaborative, genre_game_id) {
  this.gameId = gameId;
  //this.type = type;
  this.id = id;
  this.genre_game_id = genre_game_id;
  this.isCollaborative = isCollaborative;

  
}

exports.Quiz.prototype = {

  getGenre : function() {
    return genreList[this.genre_game_id];
  },

  remove : function() {
    E.quiz.removeGame(this.id);
    var genre = this.getGenre();
    if (genre) {
      genre.remove();
      E.quiz.removeGenre(this.genre_game_id);
    }
  },

 useHelp : function(data, callback) {
  var genre_game = this.getGenre();
  // If is hints or remove help
  if(data.helpType == 'hints' || data.helpType == 'remove') {
      genre_game.useRemoveHintsHelp(data, function(request){
        callback(request);
      });
  }

  // If is resource help
  else if(data.helpType == 'resource') {
    if(!data.help.selected) {
      genre_game.useResourceHelp(data, function(response) {
        callback(response);
      });
    }
    used = true;
  }

  // If is unknown help
  else {
    send.error = 'EH1';
  }
              
 },

};

exports.load = function(id, gameId, genreCode, callback) {
  if(id in list) {
    callback(list[id]);
  }
  else {
    var isCollaborative = false;
    var self = this;

    if (genreCode.indexOf('Collaborative') > -1 ) {
      var cId = "c"+new Date().getTime();
      isCollaborative = true;
      E.cquiz.load(cId, id, function(cquiz) {
        list[id] = new self.Quiz(gameId, id, isCollaborative, cId);
        genreList[cquiz.id] = cquiz;
        callback(list[id]);
      });
    }
    else {
      var iId = "i"+new Date().getTime();
       E.iquiz.load(iId, id, function(iquiz) {
        list[id] = new self.Quiz(gameId, id, isCollaborative, iId);
        genreList[iquiz.id] = iquiz;
        callback(list[id]);
      });
    }

    
  }
}

exports.get = function(id) {
  if(id in list) {
    return list[id];
  }
  console.error('Invalid Quiz Game');
  return null;
}

exports.removeGame = function(id) {
  if(id in list) {
    delete list[id];
  }
}

exports.removeGenre = function(id) {
  if(id in genreList) {
    delete genreList[id];
  }
}