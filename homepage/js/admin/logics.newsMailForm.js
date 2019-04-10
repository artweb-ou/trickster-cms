window.newsMailFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.newsmailstext_form');
		for (var i = 0; i < elements.length; i++) {
			new NewsMailForm(elements[i]);
		}
		var elements = _('.newsmailinfo_block');
		for (var i = 0; i < elements.length; i++) {
			new NewsMailInfoFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};