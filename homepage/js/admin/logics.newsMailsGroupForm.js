window.newsMailsGroupFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.newsmailsgroup_form');
		for (var i = 0; i < elements.length; i++) {
			new NewsMailsGroupForm(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};