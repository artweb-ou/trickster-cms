window.GenericIconFormComponent = function(componentElement) {
	var init = function() {
		let genericIconFormElements = _('select[data-select]', componentElement);

		[].forEach.call(genericIconFormElements, function(genericIconFormElement,i) {
			new AjaxSelectComponent(genericIconFormElement, genericIconFormElement.dataset.select, 'admin');
		});
	};
	init();
};