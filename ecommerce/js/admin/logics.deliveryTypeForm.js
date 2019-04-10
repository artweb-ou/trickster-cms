window.deliveryTypeFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.deliverytype_form');
		for (var i = 0; i < elements.length; i++) {
			new DeliveryTypeFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};