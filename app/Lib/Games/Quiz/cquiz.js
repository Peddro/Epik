
var list = {};

exports.CQuiz = function(id, quizId) {
  this.id = id;
  this.quizId = quizId;
}

exports.CQuiz.prototype = {

  useRemoveHintsHelp : function(data, callback) {
    // Get Data to Send
    var request = { playerId : data.playerId, activityId: data.activityId, helpType: data.helpType };
    switch(data.helpType) {
      case 'hints':
        E.db.getHints(data.help.selected, data.activitySource, data.gameId, function(results) {
          request.select = 1;
          request.options = results;
          callback(request);
        });
        break;

      case 'remove':
        E.db.getIncorrectAnswers(data.selected, data.activitySource, data.gameId, function(results) {
          request.select = Math.floor((results.length + 1)/2);
          request.options = results;
          callback(request);
        }, true);
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
    E.cquiz.remove(this.id);
  }

};

exports.load = function(id, quizId, callback) {
  if(id in list) {
    callback(list[id]);
  }
  else {
    list[id] = new this.CQuiz(id, quizId);  
    callback(list[id]);
  }
}

exports.get = function(id) {
  if(id in list) {
    return list[id];
  }
  console.error('Invalid CQuiz Game');
  return null;
}

exports.remove = function(id) {
  if(id in list) {
    delete list[id];
  }
}