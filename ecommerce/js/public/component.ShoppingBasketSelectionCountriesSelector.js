window.ShoppingBasketSelectionCountriesSelector = function(componentElement) {
    var countryDropDown;
    var countriesList;
    var fieldCell;
    var singleOptionElement;

    var self = this;

    var init = function() {
        if (fieldCell = _('.form_field', componentElement)[0]) {
            var parameters = {};
            parameters.changeCallback = countryDropDownChange;
            parameters.className = 'shoppingbasket_delivery_country_selector';
            parameters.optionsData = [];
            countryDropDown = window.dropDownManager.createDropDown(parameters);
            fieldCell.appendChild(countryDropDown.componentElement);
            singleOptionElement = self.makeElement('span', 'shoppingbasket_delivery_form_sole_option', fieldCell);

            controller.addListener('startApplication', updateData);
            controller.addListener('shoppingBasketUpdated', updateData);
        }
    };
    var countryDropDownChange = function() {
        window.shoppingBasketLogics.selectDeliveryCountry(countryDropDown.value);
    };
    var updateData = function() {
        countriesList = window.shoppingBasketLogics.countriesList;
        var optionsData = [];
        for (var i = 0; i < countriesList.length; i++) {
            var countryData = countriesList[i];
            if (countryData.id == window.shoppingBasketLogics.selectedCountryId) {
                optionsData.push({value: countryData.id, text: countryData.title, selected: true});
            } else {
                optionsData.push({value: countryData.id, text: countryData.title, selected: false});
            }
        }
        countryDropDown.updateOptionsData(optionsData);

        if (countriesList.length > 0) {
            componentElement.style.display = '';
            if (countriesList.length == 1) {
                countryDropDown.componentElement.style.display = 'none';
                singleOptionElement.innerHTML = countriesList[0].title;
                singleOptionElement.style.display = '';
            } else {
                countryDropDown.componentElement.style.display = '';
                singleOptionElement.style.display = 'none';
            }
        } else {
            componentElement.style.display = 'none';
        }
    };

    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionCountriesSelector.prototype);