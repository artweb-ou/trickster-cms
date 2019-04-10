window.productSelectionFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.product_selection_form');
		for (var i = elements.length; i--;) {
			new ProductSelectionFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};