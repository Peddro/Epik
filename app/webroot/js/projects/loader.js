$(document).ready(function() {
	
	/**
	 * Loading functions
	 *
	 * @package E.load
	 * @author Bruno Sampaio
	 */
	E.load = {
		
		/**
		 * Load Contents Data
		 *
		 * This method loads resources or activities data from the server, requesting the load_contents action on the projects controller.
		 * The to load list must contain a resources or activities list, otherwise finishedCallback is immediatly invoked.
		 * For resources when the data is received it first loads the resource file data and only then it invokes the respective callback.
		 * The sent and received data must be on JSON format.
		 *
		 * @param object toLoad - the list of contents to load.
		 * @param string loadingMessage - the message to display while loading.
		 * @param function beforeCallback - the function to be invoked before starting the request.
		 * @param function activitiesCallbak - the function to be invoked after receiving the activities data.
		 * @param function resourcesCallback - the function to be invoked after loading the resources data.
		 * @param function finishedCallback - the function to be invoked when all data is ready and set.
		 */
		contents : function(toLoad, loadingMessage, beforeCallback, activitiesCallback, resourcesCallback, finishedCallback) {
			var collections = E.defaults.collections;
			if(beforeCallback) beforeCallback();
			
			if(toLoad && (!$.isEmptyObject(toLoad[collections[3]]) || !$.isEmptyObject(toLoad[collections[4]]))) {
				E.ajax.start(ids.canvas);
				
				E.ajax.request(
					'get', 
					E.system.server + 'projects/load_contents', 
					{ load: JSON.stringify(toLoad) }, 
					'json', true, true, 
					function(data) {
						
						// Set Activities Data
						if(collections[4] in data) {
							activitiesCallback(data[collections[4]], collections[4]);
						}

						// Set Resources Data
						if(collections[3] in data) {
							E.game.utils.loadResources(loadingMessage, data[collections[3]], function() {
								resourcesCallback(data[collections[3]], collections[3]);
								finishedCallback();
							});
						}
						else finishedCallback();
					}, 
					function() { E.ajax.finish(ids.canvas); }
				);
			}
			else finishedCallback();
		}
		
	};
	
});