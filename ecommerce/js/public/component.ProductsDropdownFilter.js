window.ProductsDropdownFilterComponent = function(componentElement, filterData, parentComponent, listComponent) {
    var selectElement;
    var init = function() {
        if (componentElement) {
            selectElement = componentElement.querySelector('select.products_filter_dropdown');
        }
        if (selectElement) {
            eventsManager.addHandler(selectElement, 'change', change);
        }
    };

    var change = function(event) {
        listComponent.changeFilterValue(filterData.getId(), getValue());
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

    init();
};