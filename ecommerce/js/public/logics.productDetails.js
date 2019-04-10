window.productDetailsLogics = new function() {
	var initComponents = function() {
		var elements = _('.product_details');
		for (var i = 0; i < elements.length; i++) {
			new ProductDetailsComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};