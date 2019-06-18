window.ShoppingBasketSelectionForm = function(componentElement, formElement) {
    var payerContainerDisplayed = false;

    var payerCheckBoxElement;
    var payerDataElement;
    var deliveryDataElement;
    var fieldsBaseName;
    var controlsBlock;
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
            while (deliveryDataElement.firstChild) {
                deliveryDataElement.removeChild(deliveryDataElement.firstChild);
            }

            // add delivery fields, make sure post24/smartpost ones come first
            var dpdPointField, dpdRegionField, post24automatField, post24regionField, smartPostAutomatField,
                smartPostRegionField;
            for (var i = 0; i < deliveryType.deliveryFormFields.length; i++) {
                var autocomplete = deliveryType.deliveryFormFields[i].autocomplete;
                if (typeof window.dpdLogics !== 'undefined') {
                    if (autocomplete === 'dpdRegion') {
                        dpdRegionField = deliveryType.deliveryFormFields[i];
                        if (deliveryType.deliveryFormFields[i].value) {
                            dpdLogics.setCurrentRegion(deliveryType.deliveryFormFields[i].value);
                        }
                    } else if (autocomplete === 'dpdPoint') {
                        dpdPointField = deliveryType.deliveryFormFields[i];
                    }
                }
                if (typeof window.post24Logics !== 'undefined') {
                    if (autocomplete === 'post24Region') {
                        post24regionField = deliveryType.deliveryFormFields[i];
                        if (deliveryType.deliveryFormFields[i].value) {
                            post24Logics.setCurrentRegion(deliveryType.deliveryFormFields[i].value);
                        }
                    } else if (autocomplete === 'post24Automate') {
                        post24automatField = deliveryType.deliveryFormFields[i];
                    }
                }
                if (typeof window.smartPostLogics !== 'undefined') {
                    if (autocomplete === 'smartPostRegion') {
                        smartPostRegionField = deliveryType.deliveryFormFields[i];
                        if (deliveryType.deliveryFormFields[i].value) {
                            smartPostLogics.setCurrentRegion(deliveryType.deliveryFormFields[i].value);
                        }
                    } else if (autocomplete === 'smartPostAutomate') {
                        smartPostAutomatField = deliveryType.deliveryFormFields[i];
                    }
                }
            }
            var field;
            if (dpdRegionField) {
                field = new ShoppingBasketSelectionFormDpdRegion(dpdRegionField, fieldsBaseName);
                deliveryDataElement.appendChild(field.componentElement);
                if (dpdPointField) {
                    field = new ShoppingBasketSelectionFormDpdPoint(dpdPointField, fieldsBaseName);
                    deliveryDataElement.appendChild(field.componentElement);
                }
            }
            if (smartPostRegionField) {
                field = new ShoppingBasketSelectionFormSmartPostRegion(smartPostRegionField, fieldsBaseName);
                deliveryDataElement.appendChild(field.componentElement);
                if (smartPostAutomatField) {
                    field = new ShoppingBasketSelectionFormSmartPostAutomate(smartPostAutomatField, fieldsBaseName);
                    deliveryDataElement.appendChild(field.componentElement);
                }
            }
            if (post24regionField) {
                field = new ShoppingBasketSelectionFormPost24Region(post24regionField, fieldsBaseName);
                deliveryDataElement.appendChild(field.componentElement);
                if (post24automatField) {
                    field = new ShoppingBasketSelectionFormPost24Automate(post24automatField, fieldsBaseName);
                    deliveryDataElement.appendChild(field.componentElement);
                }
            }
            for (var j = 0; j < deliveryType.deliveryFormFields.length; j++) {
                var autocomplete2 = deliveryType.deliveryFormFields[j].autocomplete;
                if (autocomplete2 != 'dpdPoint' && autocomplete2 != 'dpdRegion' && autocomplete2 != 'post24Automate' && autocomplete2 != 'post24Region' && autocomplete2 != 'smartPostRegion' &&
                    autocomplete2 != 'smartPostAutomate') {
                    field = new ShoppingBasketSelectionFormField(deliveryType.deliveryFormFields[j], fieldsBaseName, formElement);
                    deliveryDataElement.appendChild(field.getComponentElement());
                }
            }
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
