window.DeliveryTypeFormComponent = function(componentElement) {
	var init = function() {
		var element = _('.deliverytype_form_prices_component', componentElement)[0];
		if (element) {
			new DeliveryTypeFormPricesComponent(element);
		}
		var element = _('.deliverytype_form_fields_component', componentElement)[0];
		if (element) {
			new DeliveryTypeFormFieldsComponent(element);
		}
	};

	init();
};

window.DeliveryTypeFormPricesComponent = function(componentElement) {
	var self = this;

	var rowsIndex = {};
	var newRowComponent = false;
	var pricesTable = false;
	var pricesRow;
	var separatorRowElement;
	var rowsContainerElement = false;

	this.pricesBaseName = '';
	var init = function() {
		separatorRowElement = _(".seperator", componentElement)[0];
		pricesTable = _('.deliverytype_form_prices_table', componentElement)[0];
		if (pricesTable) {
			rowsContainerElement = _('.deliverytype_form_prices_rows', pricesTable)[0];
			pricesRow =  _('.prices_row', pricesTable)[0];
			var element = _('.prices_new', rowsContainerElement)[0];
			if (element) {
				newRowComponent = new DeliveryTypeFormPriceNewComponent(element, self);
				self.pricesBaseName = newRowComponent.getBaseName();
			}

			var elements = _('.prices_row', rowsContainerElement);
			for (var i = 0; i < elements.length; i++) {
				var row = new DeliveryTypeFormPriceRowComponent(self);
				row.importComponentElement(elements[i]);
				rowsIndex[row.id] = row;
			}

		}
	};
	this.removeRow = function(rowId) {
		if (typeof rowsIndex[rowId] != 'undefined') {
			rowsContainerElement.removeChild(rowsIndex[rowId].componentElement);
			delete rowsIndex[rowId];
		}
	};
	this.addRow = function(id, name, price) {
		var row = new DeliveryTypeFormPriceRowComponent(self);
		row.createComponentElement(id, name, price);
		if(pricesRow) {
			rowsContainerElement.insertBefore(row.componentElement, pricesRow);
		} else {
			var rowElement = _('.form_items', rowsContainerElement)[1];
			rowsContainerElement.insertBefore(row.componentElement, rowElement);
		}

		rowsIndex[row.id] = row;
	};
	init();
};
window.DeliveryTypeFormPriceRowComponent = function(formComponent) {
	this.importComponentElement = function(componentElement) {
		self.id = componentElement.className.split('prices_row_id_')[1];
		if (removeElement = _('.prices_row_remove', componentElement)[0]) {
			eventsManager.addHandler(removeElement, 'click', removeClickHandler);
		}
		self.componentElement = componentElement;
	};
	this.createComponentElement = function(id, name, price) {
		self.id = id;

		var componentElement = document.createElement('div');
		componentElement.className = 'form_items prices_row prices_row_id_' + id;

		var cellElement = document.createElement('span');
		cellElement.className = 'form_label';
		cellElement.innerHTML = name;
		componentElement.appendChild(cellElement);

		var cellElement = document.createElement('div');
		cellElement.className = 'form_field';
		var inputElement = document.createElement('input');
		inputElement.type = 'text';
		inputElement.name = formComponent.pricesBaseName + '[' + self.id + ']';
		inputElement.className = 'input_component';
		inputElement.value = price;
		cellElement.appendChild(inputElement);
		componentElement.appendChild(cellElement);

		var cellElement = document.createElement('div');
		cellElement.className = 'form_field';
		removeElement = document.createElement('span');
		removeElement.title = window.translationsLogics.get(["deliverytype.remove"]);
		eventsManager.addHandler(removeElement, 'click', removeClickHandler);
		removeElement.className = "prices_row_remove icon icon_delete";
		cellElement.appendChild(removeElement);
		componentElement.appendChild(cellElement);

		self.componentElement = componentElement;
	};
	var removeClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		formComponent.removeRow(self.id);
	};
	var self = this;

	var removeElement;
	this.componentElement = null;
	this.id = false;
};
window.DeliveryTypeFormPriceNewComponent = function(componentElement, formComponent) {
	var init = function() {
		selectorElement = _('select.prices_new_selector', componentElement)[0];
		if (priceElement = _('.prices_new_price', componentElement)[0]) {
			baseName = priceElement.name;
			priceElement.name = '';
		}
		if (addElement = _('.prices_new_add', componentElement)[0]) {
			eventsManager.addHandler(addElement, 'click', addClickHandler);
		}
		self.componentElement = componentElement;
	};
	var addClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		formComponent.addRow(selectorElement.value, selectorElement.options[selectorElement.selectedIndex].text, priceElement.value);
	};
	this.getBaseName = function() {
		return baseName;
	};

	var self = this;

	var baseName = '';

	var addElement = false;
	var selectorElement = false;
	var priceElement = false;

	this.componentElement = false;

	init();
};

window.DeliveryTypeFormFieldsComponent = function(componentElement) {
	var init = function() {
		separatorRowElement = _(".deliverytype_form_separator", componentElement)[0];
		if (fieldsTable = _('.deliverytype_form_fields_table', componentElement)[0]) {
			rowsContainerElement = _('.deliverytype_form_fields_rows', fieldsTable)[0];

			var element = _('.fields_new', rowsContainerElement)[0];
			if (element) {
				newRowComponent = new DeliveryTypeFormFieldNewComponent(element, self);
				self.fieldsBaseName = newRowComponent.getBaseName();
			}

			var elements = _('.fields_row', rowsContainerElement);
			for (var i = 0; i < elements.length; i++) {
				var row = new DeliveryTypeFormFieldRowComponent(self);
				row.importComponentElement(elements[i]);
				rowsIndex[row.id] = row;
			}

		}
	};
	this.removeRow = function(rowId) {
		if (typeof rowsIndex[rowId] != 'undefined') {
			rowsContainerElement.removeChild(rowsIndex[rowId].componentElement);
			delete rowsIndex[rowId];
		}
	};
	this.addRow = function(id, name, checked) {
		var row = new DeliveryTypeFormFieldRowComponent(self);
		row.createComponentElement(id, name, checked);
		rowsContainerElement.insertBefore(row.componentElement, separatorRowElement);
		rowsIndex[row.id] = row;
	};
	var self = this;

	var rowsIndex = {};
	var newRowComponent = false;
	var fieldsTable = false;

	var rowsContainerElement = false;
	var separatorRowElement;

	this.fieldsBaseName = '';

	init();
};
window.DeliveryTypeFormFieldRowComponent = function(formComponent) {
	var removeElement;
	var checkBoxElement;
	var inputElement;
	this.componentElement = false;
	this.id = false;

	this.importComponentElement = function(componentElement) {
		self.id = componentElement.className.split('fields_row_id_')[1];
		if (removeElement = _('.fields_row_remove', componentElement)[0]) {
			eventsManager.addHandler(removeElement, 'click', removeClickHandler);
		}
		if (checkBoxElement = _('input.fields_required', componentElement)[0]) {
			inputElement = _('.fields_hidden', componentElement)[0];
			eventsManager.addHandler(checkBoxElement, 'change', checkBoxChangeHandler);
		}
		self.componentElement = componentElement;
	};
	this.createComponentElement = function(id, name, checked) {
		self.id = id;

		var componentElement = document.createElement('div');
		componentElement.className = 'form_items fields_row fields_row_id_' + id;

		var cellElement = document.createElement('span');
		cellElement.className = 'form_label';
		cellElement.innerHTML = name;
		componentElement.appendChild(cellElement);

		var cellElement = document.createElement('div');
		cellElement.className = 'form_field';
		checkBoxElement = document.createElement('input');
		checkBoxElement.type = 'checkbox';
		checkBoxElement.className = 'fields_required checkbox_placeholder';
		checkBoxElement.checked = checked;
		checkBoxElement.value = "1";
		cellElement.appendChild(checkBoxElement);
		window.checkBoxManager.createCheckBox(checkBoxElement);
		eventsManager.addHandler(checkBoxElement, 'change', checkBoxChangeHandler);

		inputElement = document.createElement('input');
		inputElement.type = 'hidden';
		inputElement.className = 'fields_hidden';
		inputElement.name = formComponent.fieldsBaseName + '[' + self.id + ']';
		if (checked) {
			inputElement.value = 1;
		} else {
			inputElement.value = 0;
		}

		cellElement.appendChild(inputElement);

		componentElement.appendChild(cellElement);

		var cellElement = document.createElement('td');
		removeElement = document.createElement('span');
		removeElement.title = window.translationsLogics.get(["deliverytype.remove"]);
		removeElement.className = "prices_row_remove icon icon_delete";
		eventsManager.addHandler(removeElement, 'click', removeClickHandler);
		cellElement.appendChild(removeElement);
		componentElement.appendChild(cellElement);

		self.componentElement = componentElement;
	};
	var removeClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		formComponent.removeRow(self.id);
	};
	var checkBoxChangeHandler = function(event) {
		if (checkBoxElement.checked) {
			inputElement.value = 1;
		} else {
			inputElement.value = 0;
		}
	};
	var self = this;
};
window.DeliveryTypeFormFieldNewComponent = function(componentElement, formComponent) {
	var init = function() {
		selectorElement = _('select.fields_new_selector', componentElement)[0];
		if (fieldElement = _('input.fields_new_field', componentElement)[0]) {
			baseName = fieldElement.name;
			fieldElement.name = '';
		}
		if (addElement = _('.fields_new_add', componentElement)[0]) {
			eventsManager.addHandler(addElement, 'click', addClickHandler);
		}
		self.componentElement = componentElement;
	};
	var addClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		formComponent.addRow(selectorElement.value, selectorElement.options[selectorElement.selectedIndex].text, fieldElement.checked);
	};
	this.getBaseName = function() {
		return baseName;
	};

	var self = this;

	var baseName = '';

	var addElement = false;
	var selectorElement = false;
	var fieldElement = false;

	this.componentElement = false;

	init();
};