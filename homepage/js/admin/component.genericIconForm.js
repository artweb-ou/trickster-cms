window.GenericIconFormComponent = function(componentElement) {
	var init = function() {
		var element;
		if (element = _('.genericicon_form_productselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'product', 'admin');
		}
		if (element = _('.genericicon_form_categoryselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'category', 'admin');
		}
		if (element = _('.genericicon_form_brandselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'brand', 'admin');
		}
		if (element = _('.genericicon_form_parameterselect', componentElement)[0]) {
		//	let types = ['productParameter', 'productSelection'];
			new AjaxSelectComponent(element, 'productSelectionValue', 'admin');
		}
	};
	init();
};