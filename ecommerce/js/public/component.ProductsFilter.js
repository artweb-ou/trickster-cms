window.ProductsFilterComponent = function(componentElement, listComponent) {
    let filters = [];
    let self = this;
    let titleType = 'label';
    let selectorType = 'dropdown';
    let pricePresets = true;
    let filtersData = [];

    const init = function() {

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

    this.setPricePresets = function(newPriceSelectorType) {
        pricePresets = newPriceSelectorType;
    };

    this.initFilters = function() {
        var element, i, filter;
        for (i = 0; i < filtersData.length; i++) {
            if (element = componentElement.querySelector('.products_filter_item.products_filter_' + filtersData[i].getId())) {
                if ((filtersData[i].getType() === 'price') && !pricePresets) {
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
                'pricePresets': pricePresets,
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

    init();
};