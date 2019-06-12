window.ShoppingBasketDeliveriesSelector = function(componentElement) {
    var deliveryDropDown;
    var deliveriesList;
    var fieldCell;
    var singleOptionElement;

    var self = this;

    var init = function() {
        if (fieldCell = _('.form_field', componentElement)[0]) {
            var parameters = {};
            parameters.changeCallback = deliveryDropDownChange;
            parameters.className = 'shoppingbasket_delivery_selector';
            parameters.optionsData = [];
            deliveryDropDown = window.dropDownManager.createDropDown(parameters);
            fieldCell.appendChild(deliveryDropDown.componentElement);
            singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell);
            controller.addListener('startApplication', updateData);
            controller.addListener('shoppingBasketUpdated', updateData);
        }
    };
    var deliveryDropDownChange = function() {
        tracking.checkoutOptionsTracking(2, deliveryDropDown.text);
        window.shoppingBasketLogics.selectDelivery(deliveryDropDown.value);
    };
    var updateData = function() {
        deliveriesList = window.shoppingBasketLogics.deliveryTypesList;
        var optionsData = [];
        for (var i = 0; i < deliveriesList.length; ++i) {
            var deliveryData = deliveriesList[i];
            if (deliveryData.id == window.shoppingBasketLogics.selectedDeliveryTypeId) {
                optionsData.push({value: deliveryData.id, text: deliveryData.title, selected: true});
            } else {
                optionsData.push({value: deliveryData.id, text: deliveryData.title, selected: false});
            }
        }
        deliveryDropDown.updateOptionsData(optionsData);

        if (deliveriesList.length > 0) {
            componentElement.style.display = '';
            if (deliveriesList.length == 1) {
                deliveryDropDown.componentElement.style.display = 'none';
                singleOptionElement.innerHTML = deliveriesList[0].title;
                singleOptionElement.style.display = '';
            } else {
                deliveryDropDown.componentElement.style.display = '';
                singleOptionElement.style.display = 'none';
            }
        } else {
            componentElement.style.display = 'none';
        }
    };

    init();
};
DomElementMakerMixin.call(ShoppingBasketDeliveriesSelector.prototype);