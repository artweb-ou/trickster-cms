window.ShoppingBasketSelectionFormDpdRegion = function(info, fieldsBaseName) {
    let self = this;

    let componentElement;
    let labelElement;
    let starElement;
    let fieldElement;
    let selectElement;
    let lastCountry;
    let dropdown;

    const init = function() {
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
        self.refresh();
    };

    this.refresh = function() {
        let selectedCountry = shoppingBasketLogics.getSelectedCountry();

        if (lastCountry !== selectedCountry) {
            if (selectElement) {
                selectElement.parentNode.removeChild(selectElement);
            }
            if (dropdown) {
                dropdown.componentElement.parentNode.removeChild(dropdown.componentElement);
            }

            selectElement = document.createElement('select');
            selectElement.name = fieldsBaseName + '[' + info.fieldName + ']';
            if (selectedCountry) {
                let regionsList = window.dpdLogics.getCountryRegionsList(selectedCountry.iso3166_1a2);
                for (let i = 0; i < regionsList.length; i++) {
                    let dpdInfo = regionsList[i];
                    let option = document.createElement('option');
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

            dropdown = dropDownManager.getDropDown(selectElement);
            fieldElement.appendChild(dropdown.componentElement);
            changeHandler();
        }
    };
    const changeHandler = function() {
        dpdLogics.setCurrentRegion(selectElement.value);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getId = function() {
        return info.id;
    };

    init();
};
