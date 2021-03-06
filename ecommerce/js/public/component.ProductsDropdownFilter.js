window.ProductsDropdownFilterComponent = function(componentElement, filterData, selectorType, listComponent) {
    let selectElement;
    let checkboxElements;
    const init = function() {
        if (componentElement) {
            if (selectorType === 'dropdown') {
                if (selectElement = componentElement.querySelector('select.products_filter_dropdown')) {
                    eventsManager.addHandler(selectElement, 'change', change);
                }
            } else if (selectorType === 'checkbox') {
                if (checkboxElements = componentElement.querySelectorAll('input.products_filter_checkbox')) {
                    for (let i = 0; i < checkboxElements.length; i++) {
                        eventsManager.addHandler(checkboxElements[i], 'change', change);
                    }
                }
            }
        }
    };

    const change = function() {
        listComponent.changeFilter(filterData.getId(), getValue());
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    const getValue = function() {
        if (selectorType === 'dropdown') {
            if (selectElement.selectedIndex) {
                let value = selectElement.options[selectElement.selectedIndex].value;
                if (filterData.getType() === 'parameter') {
                    return [parseInt(value, 10)];
                }
                return [value];
            }
        } else if (selectorType === 'checkbox') {
            let values = [];
            for (let i = 0; i < checkboxElements.length; i++) {
                if (checkboxElements[i].checked) {
                    if (filterData.getType() === 'price') {
                        values.push(checkboxElements[i].value);
                    } else {
                        values.push(parseInt(checkboxElements[i].value, 10));
                    }
                }
            }
            if (values.length) {
                return values;
            }
        }
        return [];
    };

    this.getType = function() {
        return filterData.getType();
    };

    init();
};