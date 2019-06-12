window.ShoppingBasketSelectionFormPost24Region = function(info, fieldsBaseName) {
    var self = this;

    var componentElement;
    var labelElement;
    var starElement;
    var fieldElement;
    var selectElement;

    this.componentElement = null;

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
            var regionsList = window.post24Logics.getCountryRegionsList(selectedCountry.iso3166_1a2);
            for (var i = 0; i < regionsList.length; i++) {
                var post24Info = regionsList[i];
                var option = document.createElement('option');
                option.text = post24Info.getName();
                option.value = post24Info.getName();
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

        self.componentElement = componentElement;
    };
    var changeHandler = function() {
        post24Logics.setCurrentRegion(selectElement.value);
    };
    init();
};
