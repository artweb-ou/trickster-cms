window.catalogueMassEditFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.catalogue_masseditor');
		for (var i = elements.length; i--;) {
			new CatalogueMassEditFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};