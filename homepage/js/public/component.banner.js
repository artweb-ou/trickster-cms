function BannerComponent(componentElement) {
	var init = function() {
		if (componentElement.href) {
			eventsManager.addHandler(componentElement, 'click', clickHandler);
		}
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		if (componentElement.target == '_blank') {
			window.open(componentElement.href);
		} else {
			document.location.href = componentElement.href;
		}
	};
	var self = this;
	init();
};