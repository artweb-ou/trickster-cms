window.ShoppingBasketSelectionCitiesSelector = function(componentElement) {
    var cityDropDown;
    var citiesList;
    var fieldCell;
    var singleOptionElement;

    var self = this;

    var init = function() {
        if (fieldCell = _('.form_field', componentElement)[0]) {
            var parameters = {};
            parameters.changeCallback = cityDropDownChange;
            parameters.className = 'shoppingbasket_city_selector';
            parameters.optionsData = [];
            cityDropDown = window.dropDownManager.createDropDown(parameters);
            fieldCell.appendChild(cityDropDown.componentElement);
            singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell);

            controller.addListener('startApplication', updateData);
            controller.addListener('shoppingBasketUpdated', updateData);
        }
    };
    var cityDropDownChange = function() {
        window.shoppingBasketLogics.selectDeliveryCity(cityDropDown.value);
    };
    var updateData = function() {
        citiesList = window.shoppingBasketLogics.getCitiesList();
        var optionsData = [];
        for (var i = 0; i < citiesList.length; ++i) {
            var cityData = citiesList[i];
            if (cityData.id == window.shoppingBasketLogics.selectedCityId) {
                optionsData.push({value: cityData.id, text: cityData.title, selected: true});
            } else {
                optionsData.push({value: cityData.id, text: cityData.title, selected: false});
            }
        }
        cityDropDown.updateOptionsData(optionsData);

        if (citiesList.length > 0) {
            componentElement.style.display = '';
            if (citiesList.length == 1) {
                cityDropDown.componentElement.style.display = 'none';
                singleOptionElement.innerHTML = citiesList[0].title;
                singleOptionElement.style.display = '';
            } else {
                cityDropDown.componentElement.style.display = '';
                singleOptionElement.style.display = 'none';
            }
        } else {
            componentElement.style.display = 'none';
        }
    };

    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionCitiesSelector.prototype);