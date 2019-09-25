window.ShoppingBasketSelectionFormDpdRegion = function(info, fieldsBaseName) {
    var self = this;

    var componentElement;
    var labelElement;
    var starElement;
    var fieldElement;
    var selectElement;

    var init = function() {
        componentElement = document.createElement('tr');
        if (info.error != '0' && info.error) {
            componentElement.className = 'form_error';
        }

        labelElement = document.createElement('td');
        labelElement.className = 'form_label';
        componentElement.appendChild(labelElement);

        starElement = document.createElement('td');
        starElement.className = 'form_star';
        componentElement.appendChild(starElement);

        fieldElement = document.createElement('td');
        fieldElement.className = 'form_field';
        componentElement.appendChild(fieldElement);

        if (info.required) {
            starElement.innerHTML = '*';
        }

        labelElement.innerHTML = info.title + ':';

        selectElement = document.createElement('select');
        selectElement.name = fieldsBaseName + '[' + info.fieldName + ']';
        var selectedCountry = shoppingBasketLogics.getSelectedCountry();
        if (selectedCountry) {
            var regionsList = window.dpdLogics.getCountryRegionsList(selectedCountry.iso3166_1a2);
            for (var i = 0; i < regionsList.length; i++) {
                var dpdInfo = regionsList[i];
                var option = document.createElement('option');
                option.text = dpdInfo.getName();
                option.value = dpdInfo.getId();
                selectElement.options.add(option);
                if (info.value && option.value == info.value) {
                    selectElement.selectedIndex = i;
                }
            }
        }
        fieldElement.appendChild(selectElement);
        eventsManager.addHandler(selectElement, 'change', changeHandler);

        var dropdown = dropDownManager.getDropDown(selectElement);
        fieldElement.appendChild(dropdown.componentElement);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getId = function() {
        return info.id;
    };
    var changeHandler = function() {
        dpdLogics.setCurrentRegion(selectElement.value);
    };
    init();
};
