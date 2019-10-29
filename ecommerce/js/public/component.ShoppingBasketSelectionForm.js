window.ShoppingBasketSelectionForm = function(componentElement, formElement) {
    var payerContainerDisplayed = false;

    var payerCheckBoxElement;
    var payerDataElement;
    var deliveryDataElement;
    var fieldsBaseName;
    var controlsBlock;
    var deliveryFieldsIndex = {};
    var init = function() {
        if (controlsBlock = componentElement.querySelector('.shoppingbasket_payer_data_controls')) {
            if (payerCheckBoxElement = controlsBlock.querySelector('input.checkbox_placeholder')) {
                if (payerDataElement = _('.shoppingbasket_payer_data', componentElement)[0]) {
                    eventsManager.addHandler(payerCheckBoxElement, 'change', checkBoxChangeHandler);
                }
            }
        }
        var fieldsBaseInput = _('.shoppingbasket_delivery_form_fieldsname', componentElement)[0];
        if (fieldsBaseInput) {
            fieldsBaseName = fieldsBaseInput.value;
        }
        var element = _('.shoppingbasket_delivery_form_country', componentElement)[0];
        if (element) {
            new ShoppingBasketSelectionCountriesSelector(element);
        }
        element = _('.shoppingbasket_delivery_form_deliverytype', componentElement)[0];
        if (element) {
            new ShoppingBasketDeliveriesSelector(element);
        }
        element = _('.shoppingbasket_delivery_form_cities', componentElement)[0];
        if (element) {
            new ShoppingBasketSelectionCitiesSelector(element);
        }
        deliveryDataElement = _('.shoppingbasket_delivery_form_data', componentElement)[0];
        controller.addListener('startApplication', updateContents);
        controller.addListener('shoppingBasketUpdated', updateContents);
    };
    var updateContents = function() {
        var deliveryType = shoppingBasketLogics.getSelectedDeliveryType();
        if (deliveryType.hasNeededReceiverFields) {
            payerCheckBoxElement.checked = true;
            eventsManager.fireEvent(payerCheckBoxElement, 'change');
            if (controlsBlock) {
                controlsBlock.style.display = '';
            }
        } else {
            payerCheckBoxElement.checked = false;
            eventsManager.fireEvent(payerCheckBoxElement, 'change');
            if (controlsBlock) {
                controlsBlock.style.display = 'none';
            }
        }
        if (deliveryType) {
            var order = [
                'dpdRegion',
                'dpdPoint',
                'post24Region',
                'post24Automate',
                'smartPostRegion',
                'smartPostAutomate',
            ];
            var field;
            var newIndex = {};

            //remove all dom elements
            while (deliveryDataElement.firstChild) {
                deliveryDataElement.removeChild(deliveryDataElement.firstChild);
            }
            deliveryType.deliveryFormFields.sort(function(a, b) {
                var aOrder = order.indexOf(a.autocomplete);
                var bOrder = order.indexOf(b.autocomplete);
                if ((aOrder < 0) && (bOrder < 0)) {
                    return 0;
                }
                if (aOrder < 0) {
                    return 1;
                }
                if (bOrder < 0) {
                    return -1;
                }
                return aOrder - bOrder;

            });
            for (var i = 0; i < deliveryType.deliveryFormFields.length; i++) {
                var fieldInfo = deliveryType.deliveryFormFields[i];
                if (typeof deliveryFieldsIndex[fieldInfo.id] !== 'undefined') {
                    field = deliveryFieldsIndex[fieldInfo.id];
                } else {
                    var autocomplete = fieldInfo.autocomplete;
                    if (autocomplete === 'dpdRegion') {
                        field = new ShoppingBasketSelectionFormDpdRegion(fieldInfo, fieldsBaseName);
                    } else if (autocomplete === 'dpdPoint') {
                        field = new ShoppingBasketSelectionFormDpdPoint(fieldInfo, fieldsBaseName);
                    } else if (autocomplete === 'post24Region') {
                        field = new ShoppingBasketSelectionFormPost24Region(fieldInfo, fieldsBaseName);
                    } else if (autocomplete === 'post24Automate') {
                        field = new ShoppingBasketSelectionFormPost24Automate(fieldInfo, fieldsBaseName);
                    } else if (autocomplete === 'smartPostRegion') {
                        field = new ShoppingBasketSelectionFormSmartPostRegion(fieldInfo, fieldsBaseName);
                    } else if (autocomplete === 'smartPostAutomate') {
                        field = new ShoppingBasketSelectionFormSmartPostAutomate(fieldInfo, fieldsBaseName);
                    } else {
                        field = new ShoppingBasketSelectionFormField(fieldInfo, fieldsBaseName, formElement);
                    }
                }
                deliveryDataElement.appendChild(field.getComponentElement());
                newIndex[field.getId()] = field;
            }
            deliveryFieldsIndex = newIndex;

        }
        updatePayerData();
    };
    var updatePayerData = function() {
        if (payerDataElement) {
            if (payerCheckBoxElement.checked) {
                hidePayerData();
            } else {
                displayPayerData();
            }
        }
    };
    var displayPayerData = function() {
        payerContainerDisplayed = true;

        TweenLite.to(payerDataElement, 0.5, {'css': {'height': payerDataElement.scrollHeight, 'opacity': 1}});
    };
    var hidePayerData = function() {
        payerContainerDisplayed = false;

        TweenLite.to(payerDataElement, 0.5, {'css': {'height': 0, 'opacity': 0}});
    };
    var checkBoxChangeHandler = function() {
        updatePayerData();
    };

    init();
};
