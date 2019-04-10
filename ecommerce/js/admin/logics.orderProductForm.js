window.OrderProductFormLogics = new function() {
	var initComponents = function() {
		var element = _('.order_product_form')[0];
		if (element) {
			new OrderProductFormComponent(element);
		}
	};
	controller.addListener('initDom', initComponents);
};