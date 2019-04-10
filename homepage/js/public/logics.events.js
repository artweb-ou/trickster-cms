window.eventsLogics = new function() {
	var initComponents = function() {
		var elements = _('.eventslist');
		for (var i = 0; i < elements.length; i++) {
			new EventsListComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};