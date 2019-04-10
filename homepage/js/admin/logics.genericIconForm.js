window.genericIconFormLogics = new function() {
	var initComponents = function() {
		var element = _('.genericicon_form')[0];
		if (element) {
			new GenericIconFormComponent(element);
		}
	};
	controller.addListener('initDom', initComponents);
};