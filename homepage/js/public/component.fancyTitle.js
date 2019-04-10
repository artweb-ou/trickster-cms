window.FancyTitleComponent = function(componentElement) {
	var init = function() {
		if (componentElement.title) {
			new TipPopupComponent(componentElement, componentElement.title);
			componentElement.title = '';
		}
	};
	init();
};