window.CatalogueMassEditFormComponent = function(componentElement) {
	var targetAllCheckboxElement, targetsInputElement;
	var init = function() {
		eventsManager.addHandler(componentElement, 'submit', submit);
		var element;
		if (element = _('select.catalogue_masseditor_categoryselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'category', 'admin');
		}
		if (element = _('select.catalogue_masseditor_brandselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'brand', 'admin');
		}
		if (element = _('select.catalogue_masseditor_discountselect', componentElement)[0]) {
			new AjaxSelectComponent(element, 'discount', 'admin');
		}
		if (element = _('.catalogue_masseditor_targetall_checkbox', componentElement)[0]) {
			targetAllCheckboxElement = element;
		}
		if (element = _('.catalogue_masseditor_targets_input', componentElement)[0]) {
			targetsInputElement = element;
		}
	};
	var submit = function() {
		targetsInputElement.value = '';
		if (!targetAllCheckboxElement.checked) {
			var targetsIds = [];
			var checkboxElements = _('input[type=checkbox]');
			for (var i = checkboxElements.length; i--;) {
				if (!checkboxElements[i].checked) {
					continue;
				}
				var id = checkboxElements[i].name;
				var strpos = id.indexOf('elements][');
				if (strpos >= 0) {
					id = id.substring(strpos + 10);
					id = id.substring(0, id.length - 1);
					targetsIds.push(id);
				}
			}
			if (targetsIds.length) {
				targetsInputElement.value = targetsIds.join(',');
			}
		}
	};
	init();
};