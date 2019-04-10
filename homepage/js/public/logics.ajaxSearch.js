window.ajaxSearchLogics = new function() {
	var self = this;
	var initComponents = function() {
		var elements = _('.left_panel_ajaxsearch_block');
		for (var i = 0; i < elements.length; i++) {
			new HeaderAjaxSearchComponent(elements[i]);
		}
	};
	var receiveData = function(responseStatus, requestName, responseData, callBack) {
		if (responseStatus == 'success' && responseData) {
			callBack(responseData);
		} else {
			controller.fireEvent('ajaxSearchResultsFailure', responseData);
		}
	};
	this.sendQuery = function(callBack, query, types, apiMode) {
		var url = '/ajaxSearch/mode:' + apiMode + '/types:' + types + '/?query=' + query;
		var request = new JsonRequest(url, function(responseStatus, requestName, responseData) {
			return receiveData(responseStatus, requestName, responseData, callBack);
		}, 'ajaxSearch');
		request.send();
	};
	controller.addListener('initDom', initComponents);
};