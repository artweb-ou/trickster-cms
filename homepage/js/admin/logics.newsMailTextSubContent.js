window.newsMailTextSubContentLogics = new function() {
	var initComponents = function() {
		var elements = _('.newsmailtextsubcontent_form');
		for (var i = elements.length; i--;) {
			new NewsMailTextSubContentFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};