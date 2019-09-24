window.ProductsDropdownFilterComponent = function(componentElement, filterData, listComponent) {
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
        listComponent.changeFilters();
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    this.getValue = function() {
        if (selectElement.options && selectElement.options[selectElement.selectedIndex].value) {
            return selectElement.options[selectElement.selectedIndex].value;
        }
        return '';
    };

    this.getType = function() {
        return filterData.getType();
    };

    this.resetValue = function() {
        selectElement.value = '';
    };
    init();
};