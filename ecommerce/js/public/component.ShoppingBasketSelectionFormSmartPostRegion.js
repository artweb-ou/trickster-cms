window.ShoppingBasketSelectionFormSmartPostRegion = function(info, fieldsBaseName) {
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

        var regionsList = window.smartPostLogics.getRegionsList();
        for (var i = 0; i < regionsList.length; i++) {
            var smartPostInfo = regionsList[i];
            var option = document.createElement('option');
            option.text = smartPostInfo.getName();
            option.value = smartPostInfo.getName();
            selectElement.options.add(option);
            if (info.value && option.value == info.value) {
                selectElement.selectedIndex = i;
            }
        }
        fieldElement.appendChild(selectElement);
        eventsManager.addHandler(selectElement, 'change', changeHandler);

        var dropdown = dropDownManager.getDropDown(selectElement);
        fieldElement.appendChild(dropdown.componentElement);

    };
    var changeHandler = function() {
        smartPostLogics.setCurrentRegion(selectElement.value);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getId = function() {
        return info.id;
    };
    init();
};
