window.feedbackLogics = new function() {
	var init = function() {
		var componentElement = _('.feedback_form');
		for (var i = 0; i < componentElement.length; i++) {
			new AjaxFormComponent(componentElement[i], tracking.feedbackTracking);
		}
		var elements = _('.feedback_with_file_upload');
		for (var i = 0; i < elements.length; i++) {
			new FeedbackDragAndDropComponent(elements[i]);
		}
	};
	controller.addListener('initDom', init);
};