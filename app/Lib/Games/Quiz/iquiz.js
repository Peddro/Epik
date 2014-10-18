var list = {};

exports.IQuiz = function(id, quizId) {
  this.id = id;
  this.quizId = quizId;
}

exports.IQuiz.prototype = {

  useRemoveHintsHelp : function(data, callback) {;
    var response = { activityId : data.activityId, helpType : data.helpType, list : [] };
    switch(data.helpType) {
      
      // Select Random Hints
      case 'hints':
        E.db.getHints(data.help.selected, data.activitySource, data.gameId, function(results) {
          response.list.push(results[E.utils.random(results.length - 1)]);
          callback(response);
        });
        break;

      // Select Random Answers
      case 'remove':
        E.db.getIncorrectAnswers(null, data.activitySource, data.gameId, function(results) {
          var i = 0, max = Math.floor((results.length + 1)/2), selected = {};
          while(i < max) {
            var index = E.utils.random(results.length - 1);
            if(!(index in selected)) {
              response.list.push(results[index]);
              selected[index] = true;
              i++;
            }
          }
          callback(response);
        });
        break;
    }
  },

  useResourceHelp : function(data, callback) {
    var response = { activityId : data.activityId, helpType : data.helpType, list : [] };
      
    E.db.getActivityResources(null, data.activitySource, data.gameId, function(results) {
      response.list.push(results[E.utils.random(results.length - 1)]);
      callback(response);
    });
  },

  remove : function() {
    E.iquiz.remove(this.quizId);
  }

};

exports.load = function(id, quizId, callback) {
 if(id in list) {
    callback(list[id]);
  }
  else {
    list[id] = new this.IQuiz(id, quizId);  
    callback(list[id]);
  }
}

exports.get = function(id) {
  if(id in list) {
    return list[id];
  }
  console.error('Invalid IQuiz Game');
  return null;
}

exports.remove = function(id) {
  if(id in list) {
    delete list[id];
  }
}