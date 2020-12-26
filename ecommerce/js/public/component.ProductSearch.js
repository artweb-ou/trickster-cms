window.ProductSearchComponent = function(componentElement) {
    var filtersComponent;
    var sorterComponent;
    var self = this;
    var productsListData;
    var productSearchData;
    var id;
    var init = function() {
        id = parseInt(componentElement.dataset.id, 10);
        if (productSearchData = window.productLogics.getProductSearchForm(id)) {
            controller.addListener('productsSearchFormUpdated', updateHandler);

            createProductsSorterComponent();
            createProductsFilterComponent();
            var resetElement = componentElement.querySelector('.productsearch_reset');
            if (resetElement) {
                eventsManager.addHandler(resetElement, 'click', reset);
            }
        }

        productsListData = productLogics.getMainProductsList();
    };

    var updateHandler = function(updatedId) {
        if (updatedId === productSearchData.id) {
            if (filtersComponent) {
                filtersComponent.updateData(productSearchData.getFilters());
                filtersComponent.rebuildFilters();
            }
            if (sorterComponent) {
                sorterComponent.updateData(productsListData.filterSort+';'+productsListData.filterOrder);
            }
        }
    };

    var createProductsFilterComponent = function() {
        var element = componentElement.querySelector('.products_filter');
        if (element) {
            filtersComponent = new ProductsFilterComponent(element, self);
            if (productSearchData.checkboxesForParameters) {
                filtersComponent.setTitleType('label');
                filtersComponent.setSelectorType('checkbox');
            } else {
                filtersComponent.setTitleType('option');
                filtersComponent.setSelectorType('dropdown');
            }

            filtersComponent.setPricePresets(productSearchData.pricePresets);

            filtersComponent.updateData(productSearchData.getFilters());
            filtersComponent.initFilters();
        }
    };

    var createProductsSorterComponent = function() {
        var element = componentElement.querySelector('.products_sorter');
        if (element) {
            sorterComponent = new ProductsDropdownSorterComponent(element, self);
        }
    };

    this.changeFilter = function(id, value) {
        productsListData.changeFilter(id, value);
    };

    this.changeSorting = function(sorting) {
        productsListData.changeSorting(sorting);
    };

    var reset = function() {
        productsListData.reset();
    };

    init();
};