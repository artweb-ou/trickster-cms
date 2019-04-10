function CategoryShortComponent(componentElement) {
	var linkElement;

	var init = function() {
		if (linkElement = _(".category_short_link", componentElement)[0]) {
			eventsManager.addHandler(componentElement, "click", clickHandler);

			var innerLinks = _("a", componentElement);
			if (innerLinks.length > 0) {
				for (var i = innerLinks.length; i--;) {
					eventsManager.addHandler(innerLinks[i], "click", innerLinkClick);
				}
			}
		}
	};

	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		document.location.href = linkElement.href;
	};
	var innerLinkClick = function(event) {
		eventsManager.cancelBubbling(event);
	};
	init();
}