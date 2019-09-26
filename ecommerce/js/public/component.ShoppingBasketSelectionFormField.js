window.ShoppingBasketSelectionFormField = function(info, fieldsBaseName, formElement) {
    var self = this;

    var componentElement;
    var labelElement;
    var starElement;
    var fieldComponent;
    var textareaElement;

    var init = function() {
        var fieldElement, helper, parameters;

        componentElement = document.createElement('tr');
        if (info.error != '0' && info.error) {
            componentElement.className = 'form_error';
        }
        labelElement = self.makeElement('td', 'form_label', componentElement);
        starElement = self.makeElement('td', 'form_star', componentElement);
        fieldElement = self.makeElement('td', 'form_field', componentElement);

        if (info.required) {
            starElement.innerHTML = '*';
        }
        labelElement.innerHTML = info.title;
        if (info.fieldType === 'select') {
            parameters = {};
            parameters.className = 'shoppingbasket_delivery_form_dropdown';
            parameters.optionsData = info.options;
            parameters.name = fieldsBaseName + '[' + info.fieldName + ']';
            fieldComponent = dropDownManager.createDropDown(parameters);
            if (info.value) {
                fieldComponent.setValue(info.value);
            }
            fieldElement.appendChild(fieldComponent.componentElement);
        } else if (info.fieldType === 'textarea') {
            textareaElement = document.createElement('textarea');
            textareaElement.className = 'textarea_component';
            textareaElement.setAttribute('name', fieldsBaseName + '[' + info.fieldName + ']');
            if (info.value) {
                textareaElement.value = info.value;
            }
            new TextareaComponent(textareaElement);
            fieldElement.appendChild(textareaElement);
            if (info.helpLinkUrl && info.helpLinkText) {
                helper = new ShoppingBasketSelectionFormFieldHelperComponent(info.helpLinkUrl, info.helpLinkText);
                fieldElement.appendChild(helper.getComponentElement());
            }
        } else {
            parameters = {
                'name': fieldsBaseName + '[' + info.fieldName + ']',
                'value': info.value,
            };
            if (info.autocomplete === 'vatNumber') {
                parameters.inputClass = 'check_vat_number_input';
                fieldElement.classList.add('shoppingbasket_delivery_vatnumber');
            }
            fieldComponent = new InputComponent(parameters);
            fieldElement.appendChild(fieldComponent.componentElement);
            if (info.autocomplete === 'vatNumber') {
                var checkButton = document.createElement('button');
                checkButton.innerHTML = window.translationsLogics.get('shoppingbasket.checkvat');
                checkButton.className = 'button check_vat_button';
                fieldElement.appendChild(checkButton);
                eventsManager.addHandler(checkButton, 'click', checkVatHandler);
            }
            eventsManager.addHandler(fieldComponent.inputElement, 'keydown', checkKey);
            if (info.helpLinkUrl && info.helpLinkText) {
                helper = new ShoppingBasketSelectionFormFieldHelperComponent(info.helpLinkUrl, info.helpLinkText);
                fieldElement.appendChild(helper.getComponentElement());
            }
        }
    };
    var checkVatHandler = function(event) {
        event.preventDefault();
        shoppingBasketLogics.checkVatNumber(fieldComponent.componentElement.value);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getId = function() {
        return info.id;
    };
    var checkKey = function(event) {
        if (event.keyCode === 13) {
            formElement.submit();
        }
    };
    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionFormField.prototype);