window.mapFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.map_form');
		for (var i = elements.length; i--;) {
			new MapFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};