window.SelectedProductsFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.selectedproducts_form');
		for (var i = elements.length; i--;) {
			new SelectedProductsFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};