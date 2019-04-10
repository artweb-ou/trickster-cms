window.selectedProductsLogics = new function() {
	var initComponents = function() {
		var elements = _('.selectedproducts_content_scrolltype');
		for (var i = elements.length; i--;) {
			new SelectedProductsScrollComponent(elements[i]);
		}
		var elements = _('.selectedproducts_column');
		for (var i = elements.length; i--;) {
			new SelectedProductsColumnComponent(elements[i]);
		}
	};
	controller.addListener('DOMContentReady', initComponents);
};