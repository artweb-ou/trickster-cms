window.ButtonComponent = function(componentElement) {
	var requestParameters = [];
	var currentElementId;
	var  actionName;
	var init = function() {
		currentElementId = window.currentElementId;
		actionName = componentElement.getAttribute('control-action');
		componentElement.removeAttribute('control-action');
		eventsManager.addHandler(componentElement, 'click', sendRequest);
	};
	var sendRequest = function() {
		var requestURL = window.location.href  + 'id:' + currentElementId + '/action:' + actionName;
		window.location.replace(requestURL);
	};
	init();
};