window.ProductSearchFormComponent = function(componentElement) {
	var dependentElement, parametersElement;
	var priceElement, pricePresetsElement, priceIntervalElemen;

	var init = function() {
		parametersElement =_('.productsearch_form_parameters ', componentElement)[0];
		new AjaxSelectComponent(parametersElement, 'productParameter,productSelection', 'admin');
		parametersElement = _('.productsearch_parameters', componentElement)[0];
		dependentElement = _('input.productsearch_form_dependent_checkbox', componentElement)[0];
		checkDependentCheckbox();
		eventsManager.addHandler(dependentElement, 'change', checkDependentCheckbox);

		priceElement = _('input.productsearch_form_price_checkbox', componentElement)[0];
		pricePresetsElement = _('.form_field_pricePresets', componentElement)[0];
		priceIntervalElemen = _('.productsearch_form_price_interval', componentElement)[0];
		checkPriceCheckbox();
		eventsManager.addHandler(priceElement, 'change', checkPriceCheckbox);
	};
	var checkDependentCheckbox = function() {
		if (dependentElement.checked) {
			parametersElement.style.display = 'none';
		} else {
			parametersElement.style.display = '';
		}
	};
	var checkPriceCheckbox = function() {
		if (priceElement.checked) {
			pricePresetsElement.style.display = '';
			priceIntervalElemen.style.display = '';
		} else {
			pricePresetsElement.style.display = 'none';
			priceIntervalElemen.style.display = 'none';
		}
	};
	init();
	controller.addListener('initDom', init);
};