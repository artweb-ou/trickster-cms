window.ProductsDropdownLimitComponent = function(componentElement, listComponent) {
    var selectElement;
    var init = function() {
        if (componentElement) {
            selectElement = componentElement.querySelector('select.products_limit_dropdown');
        }
        if (selectElement) {
            eventsManager.addHandler(selectElement, 'change', change);
        }
    };

    var change = function(event) {
        listComponent.changeLimit(getValue());
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