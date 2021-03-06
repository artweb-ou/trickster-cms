window.ProductsDropdownSorterComponent = function(componentElement, listComponent) {
    var selectElement;
    var init = function() {
        if (componentElement) {
            selectElement = componentElement.querySelector('select.products_filter_dropdown');
        }
        if (selectElement) {
            eventsManager.addHandler(selectElement, 'change', change);
        }
    };

    var change = function() {
        listComponent.changeSorting(getValue());
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    var getValue = function() {
        if (selectElement.options && selectElement.options[selectElement.selectedIndex].value) {
            return selectElement.options[selectElement.selectedIndex].value;
        }
        return '';
    };

    this.updateData = function(value) {
        selectElement.value = value;
        dropDownManager.updateDropDown(selectElement);
    };

    init();
};