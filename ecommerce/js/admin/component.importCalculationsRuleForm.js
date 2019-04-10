window.ImportCalculationsRuleFormComponent = function(componentElement) {
	var containerElement, actionElement, modifierElement;
	var init = function() {
		if (containerElement = componentElement.querySelector('.importcalculations_rules_item')) {
			var inputPlaceholder = containerElement.querySelector('input');
			if (inputPlaceholder && inputPlaceholder.name) {
				var inputBaseName = inputPlaceholder.name;
				var newRuleComponent = new ImportCalculationsRuleComponent(inputBaseName);
				containerElement.appendChild(newRuleComponent.getComponentElement());
			}
		}
		actionElement = _('select.importcalculationsrule_form_action', componentElement)[0];
		modifierElement = _('.importcalculationsrule_form_modifier', componentElement)[0];
		if (actionElement && modifierElement) {
			refresh();
			eventsManager.addHandler(actionElement, 'change', refresh);
		}
	};
	var refresh = function() {
		if (actionElement.value == 'use_rrp') {
			modifierElement.style.display = 'none';
		} else {
			modifierElement.style.display = '';
		}
	};
	init();
};
window.ImportCalculationsRuleComponent = function(inputBaseName) {
	var componentElement;
	var containerElement;
	var addButton;
	var ruleComponents = [];
	var self = this;
	var init = function() {
		componentElement = self.makeElement('div', 'importcalculationsrule_block');
		containerElement = self.makeElement('div', 'importcalculationsrule_container', componentElement);

		var rulesList = importCalculationsRuleLogics.getRulesList();

		for (var i in rulesList) {
			addNewComponent(rulesList[i]);
		}

		addButton = self.makeElement('span', 'button', componentElement);
		addButton.innerHTML = translationsLogics.get('importcalculationsrule.add');
		eventsManager.addHandler(addButton, 'click', addButtonClickHandler);
	};
	var addButtonClickHandler = function() {
		addNewComponent();
	};
	var addNewComponent = function(ruleInfo) {
		if (!ruleInfo) {
			ruleInfo = false;
		}
		var item = new ImportCalculationsRuleItemComponent(ruleInfo, self, inputBaseName + '[' + ruleComponents.length + ']');
		ruleComponents.push(item);
		containerElement.appendChild(item.getComponentElement());
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.removeRuleItem = function(item) {
		for (var i = 0; i < ruleComponents.length; i++) {
			if (ruleComponents[i] == item) {
				containerElement.removeChild(item.getComponentElement());
				ruleComponents.splice(i, 1);
			}
		}
		self.checkSelectors();
	};
	this.checkSelectors = function() {
		var categoriesIds = [];
		for (var i = ruleComponents.length; i--;) {
			var ajaxSelect = ruleComponents[i].getAjaxSelector();
			if (ajaxSelect && ajaxSelect.getTypes() == 'category') {
				var ajaxSelectValues = ajaxSelect.getValues();
				if (ajaxSelectValues.length > 0) {
					categoriesIds = categoriesIds.concat(ajaxSelectValues);
				}
			}
		}
		var filterString = categoriesIds.length > 0 ? 'brandCategory-' + categoriesIds.join(',') : '';

		for (var i = ruleComponents.length; i--;) {
			var ajaxSelect = ruleComponents[i].getAjaxSelector();
			if (ajaxSelect && ajaxSelect.getTypes() == 'brand') {
				ajaxSelect.setFilters(filterString);
			}
		}
	};
	init();
};
DomElementMakerMixin.call(ImportCalculationsRuleComponent.prototype);

window.ImportCalculationsRuleItemComponent = function(ruleInfo, parentComponent, inputBaseName) {
	var componentElement;
	var containerElement;
	var removeButton;
	var typeDropdown;
	var ajaxSelector;
	var selectElement;
	var priceStartInputElement;
	var priceEndInputElement;
	var importPluginDropdown;
	var self = this;
	var init = function() {
		componentElement = self.makeElement('div', 'importcalculationsrule_item_block');
		containerElement = self.makeElement('div', 'importcalculationsrule_item_container', componentElement);
		var optionsData = [
			{'value': '', 'text': translationsLogics.get('importcalculationsrule.type_select')},
			{'value': 'category', 'text': translationsLogics.get('importcalculationsrule.type_category')},
			{'value': 'brand', 'text': translationsLogics.get('importcalculationsrule.type_brand')},
			{'value': 'product', 'text': translationsLogics.get('importcalculationsrule.type_product')},
			{'value': 'price', 'text': translationsLogics.get('importcalculationsrule.type_price')},
			{'value': 'import_plugin', 'text': translationsLogics.get('importcalculationsrule.type_import_plugin')}
		];
		typeDropdown = dropDownManager.createDropDown({
			'optionsData': optionsData,
			'name': inputBaseName + '[type]',
			'changeCallback': typeChangeCallback,
			'className': "importcalculationsrule_item_typeselector"
		});
		containerElement.appendChild(typeDropdown.getComponentElement());

		removeButton = self.makeElement('button', {'className': 'button important importcalculationsrule_item_remove', 'type': 'button'}, componentElement);
		self.makeElement('span', 'icon icon_delete', removeButton);
		self.makeElement('span', '', removeButton).innerHTML = translationsLogics.get('importcalculationsrule.remove');

		eventsManager.addHandler(removeButton, 'click', removeButtonClickHandler);

		if (ruleInfo) {
			importInfo(ruleInfo);
		}
	};
	var importInfo = function(ruleInfo) {
		if (typeof ruleInfo.type != 'undefined') {
			typeDropdown.setValue(ruleInfo.type);
			if (typeof ruleInfo.value != 'undefined' && ruleInfo.value.length > 0) {
				switch(ruleInfo.type) {
					case 'brand':
					case 'product':
					case 'category':
						for (var i = 0; i < ruleInfo.value.length; i++) {
							ajaxSelector.addOption(ruleInfo.value[i].value, ruleInfo.value[i].title);
						}
						break;
					case 'price':
						priceStartInputElement.value = ruleInfo.value[0];
						priceEndInputElement.value = ruleInfo.value[1];
						break;
					case 'import_plugin':
						importPluginDropdown.setValue(ruleInfo.value);
				}
			}
		}
	};
	var typeChangeCallback = function() {
		var dropdownValue = typeDropdown.getValue();
		switch(dropdownValue) {
			case 'brand':
			case 'product':
			case 'category':
				createAjaxSelector(dropdownValue);
				break;
			case 'price':
				createPriceRangeInputs();
				break;
			case 'import_plugin':
				createImportPluginDropdown();
		}
		parentComponent.checkSelectors();
	};
	var createImportPluginDropdown = function() {
		destroyExistingControls();
		var importPluginsNames = importCalculationsRuleLogics.getImportPluginsNames();
		if (importPluginsNames.length > 0) {
			var optionsData = [];
			for (var i = 0; i < importPluginsNames.length; ++i) {
				var name = importPluginsNames[i];
				optionsData[optionsData.length] = {'value': name, 'text': name};
			}
			importPluginDropdown = dropDownManager.createDropDown({
				'optionsData': optionsData,
				'name': inputBaseName + '[value]',
				'className': "importcalculationsrule_item_plugin_selector"
			});
			containerElement.appendChild(importPluginDropdown.getComponentElement());
		}
	};
	var createPriceRangeInputs = function() {
		destroyExistingControls();
		priceStartInputElement = self.makeElement('input', 'importcalculationsrule_item_price input_component', containerElement);
		priceStartInputElement.name = inputBaseName + '[value][0]';
		priceEndInputElement = self.makeElement('input', 'importcalculationsrule_item_price input_component', containerElement);
		priceEndInputElement.name = inputBaseName + '[value][1]';
	};
	var createAjaxSelector = function(type) {
		destroyExistingControls();
		selectElement = document.createElement('select');
		selectElement.name = inputBaseName + '[value][]';
		selectElement.multiple = true;

		containerElement.appendChild(selectElement);

		ajaxSelector = new AjaxSelectComponent(selectElement, type, 'admin', ajaxSelectChange);
	};
	var ajaxSelectChange = function() {
		parentComponent.checkSelectors();
	};
	var removeButtonClickHandler = function() {
		parentComponent.removeRuleItem(self);
	};
	var destroyExistingControls = function() {
		if (ajaxSelector) {
			containerElement.removeChild(ajaxSelector.getComponentElement());
			ajaxSelector = null;
		}
		if (selectElement && selectElement.parentNode) {
			containerElement.removeChild(selectElement);
			selectElement = null;
		}
		if (priceStartInputElement) {
			containerElement.removeChild(priceStartInputElement);
			priceStartInputElement = null;
		}
		if (priceEndInputElement) {
			containerElement.removeChild(priceEndInputElement);
			priceEndInputElement = null;
		}
		if (importPluginDropdown) {
			containerElement.removeChild(importPluginDropdown.getComponentElement());
			importPluginDropdown = null;
		}
	};
	this.getAjaxSelector = function() {
		return ajaxSelector;
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
DomElementMakerMixin.call(ImportCalculationsRuleItemComponent.prototype);