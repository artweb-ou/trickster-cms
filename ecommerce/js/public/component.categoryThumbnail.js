window.CategoryThumbnailComponent = function(componentElement) {
	var self = this;

	var categoryUrl;
	var init = function() {
		var buttonElement;
		if (buttonElement = _(".category_thumbnail_button", componentElement)[0]) {
			categoryUrl = buttonElement.href;
			eventsManager.addHandler(componentElement, "click", clickHandler)
		}
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		document.location.href = categoryUrl;
	};
	init();
};