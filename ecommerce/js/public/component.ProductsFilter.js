window.ProductsFilterComponent = function(componentElement, listComponent) {
    var filters = [];
    var self = this;
    var titleType = 'label';
    var selectorType = 'dropdown';
    var priceSelectorType = 'presets';
    var filtersData = [];

    var init = function() {

    };
    this.updateData = function(newData) {
        filtersData = newData;
    };

    this.setTitleType = function(newTitleType) {
        titleType = newTitleType;
    };

    this.setSelectorType = function(newSelectorType) {
        selectorType = newSelectorType;
    };

    this.setPriceSelectorType = function(newPriceSelectorType) {
        priceSelectorType = newPriceSelectorType;
    };

    this.initFilters = function() {
        var element, i, filter;
        for (i = 0; i < filtersData.length; i++) {
            if (element = componentElement.querySelector('.products_filter_item.products_filter_' + filtersData[i].getId())) {
                if ((filtersData[i].getType() === 'price') && (priceSelectorType === 'interval')) {
                    filter = new ProductsPriceFilterComponent(element, filtersData[i], listComponent);
                } else {
                    filter = new ProductsDropdownFilterComponent(element, filtersData[i], selectorType, listComponent);
                }
                filters.push(filter);
            }
        }
    };
    this.rebuildFilters = function() {
        while (componentElement.firstChild) {
            componentElement.removeChild(componentElement.firstChild);
        }
        filters = [];
        var html = '';
        for (var i = 0; i < filtersData.length; i++) {
            var data = {
                'titleType': titleType,
                'selectorType': selectorType,
                'filter': filtersData[i],
            };
            html += smartyRenderer.fetch('component.productsfilter_item.tpl', data);
        }
        componentElement.innerHTML = html;
        if (selectorType === 'dropdown') {
            dropDownManager.initDropdowns(componentElement);
        } else if (selectorType === 'checkbox') {
            checkBoxManager.initCheckboxes(componentElement);
        }
        self.initFilters();
    };
    
    self.resetFilters = function() {
        var i;
        for (i = 0; i < filters.length; i++) {
            filters[i].resetValue();
        }
    };

    init();
};