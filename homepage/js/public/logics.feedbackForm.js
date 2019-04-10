window.feedbackLogics = new function() {
	var init = function() {
		var componentElement = _('.feedback_form');
		for (var i = 0; i < componentElement.length; i++) {
			new AjaxFormComponent(componentElement[i], tracking.feedbackTracking);
		}
	};
	controller.addListener('initDom', init);
};