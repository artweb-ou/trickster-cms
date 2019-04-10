window.categoryShortLogics = new function() {
	var initComponents = function() {
		var elements = _('.category_short');
		for (var i = 0; i < elements.length; i++) {
			new CategoryShortComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};