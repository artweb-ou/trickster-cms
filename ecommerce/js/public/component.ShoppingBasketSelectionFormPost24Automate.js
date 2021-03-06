window.ShoppingBasketSelectionFormPost24Automate = function(info, fieldsBaseName) {
    var componentElement;
    var labelElement;
    var starElement;
    var fieldElement;
    var selectElement;
    var dropdown;

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
        fieldElement.appendChild(selectElement);
        dropdown = dropDownManager.getDropDown(selectElement);
        fieldElement.appendChild(dropdown.componentElement);

        fillSelect();

        if (info.value) {
            for (var i = selectElement.options.length; i--;) {
                if (selectElement.options[i].value == info.value) {
                    selectElement.selectedIndex = i;
                    dropdown.update();
                    break;
                }
            }
        }
        controller.addListener('post24RegionSelected', post24RegionSelectedHandler);
    };

    this.refresh = function() {
        fillSelect();
    };
    var post24RegionSelectedHandler = function() {
        fillSelect();
    };
    var fillSelect = function() {
        var region = window.post24Logics.getCurrentRegion();
        if (region) {
            while (selectElement.options.length > 0) {
                selectElement.options.remove(selectElement.options[0]);
            }

            var list = region.getAutomatesList();
            for (var i = 0; i < list.length; i++) {
                var post24Info = list[i];
                var option = document.createElement('option');
                option.text = post24Info.getFullTitle();
                option.value = post24Info.getFullTitle();
                selectElement.options.add(option);
            }
            dropdown.update();
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getId = function() {
        return info.id;
    };
    init();
};
