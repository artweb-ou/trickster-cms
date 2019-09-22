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
        }

        if (productsListData = productLogics.getMainProductsList()) {

            // var elements, i;
            // elements = componentElement.querySelectorAll('select.products_filter_dropdown_dropdown');
            // for (i = elements.length; i--;) {
            //     filters[filters.length] = new ProductsDropdownFilterComponent(elements[i], filtersData[i], self, listComponent);
            // }
            // elements = componentElement.querySelectorAll('.products_filter_checkboxes');
            // for (i = elements.length; i--;) {
            //     filters[filters.length] = new ProductsCheckboxesFilterComponent(elements[i], self);
            // }
            // elements = componentElement.querySelectorAll('.products_filter_price');
            // for (i = elements.length; i--;) {
            //     filters[filters.length] = new ProductsFilterPriceComponent(elements[i], self);
            // }
            // sortSelectElement = componentElement.querySelector('select.productsearch_sortselect');
            // if (sortSelectElement) {
            //     eventsManager.addHandler(sortSelectElement, 'change', sortChange);
            // }
            // var resetElement = componentElement.querySelector('.productsearch_reset');
            // if (resetElement) {
            //     eventsManager.addHandler(resetElement, 'click', reset);
            // }

            // var submitElement = componentElement.querySelector('.productsearch_submit');
            // if (submitElement) {
            //     eventsManager.addHandler(submitElement, 'click', submitForm);
            // }
            // controller.addListener('TabsComponent.tabActivated', tabActivated);
        }

    };
    var updateHandler = function(updatedId) {
        if (updatedId === productSearchData.id) {
            if (filtersComponent) {
                filtersComponent.updateData(productSearchData.getFilters());
                filtersComponent.rebuildFilters();
            }
        }
    };


    var createProductsFilterComponent = function() {
        var element = componentElement.querySelector('.products_filter');
        if (element) {
            filtersComponent = new ProductsFilterComponent(element, self);
            filtersComponent.setTitleType('option');
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

    this.changeFilterValue = function(type, value) {
        productsListData.changeFilter(type, value);
    };

    this.changeSorting = function(sorting) {
        productsListData.changeSorting(sorting);
    };

    this.changeLimit = function(limit) {
        productsListData.changeLimit(limit);
    };

    // var tabActivated = function() {
    //     for (var i = filters.length; i--;) {
    //         if (filters[i] instanceof ProductsFilterPriceComponent) {
    //             filters[i].refresh();
    //         }
    //     }
    // };

    // var sortChange = function() {
    //     self.refresh();
    // };

    // var reset = function() {
    //     // document.location.href = window.productsListElementUrl + 'productsearch:1/';
    //     // document.location.href = window.productsListElementUrl;
    // };

    // this.refresh = function(changedFilter) {
    //     var arguments = {};
    //     var baseUrl = '';
    //     var i, value;
    //     for (i = filters.length; i--;) {
    //         if (filters[i].getType() !== 'category') {
    //             filters[i].modifyFilterArguments(arguments);
    //         }
    //     }
    //     if (changedFilter && changedFilter.getType() === 'category') {
    //         value = changedFilter.getValue();
    //         //if category was selected, we should move into selected category
    //         if (value && window.categoriesUrls[value]) {
    //             baseUrl = window.categoriesUrls[value];
    //         } else {
    //             //otherwise category was set to "None", so we should move into product catalogue itself
    //             baseUrl = window.productSearchCatalogueUrl;
    //         }
    //     }
    //
    //     if (!baseUrl) {
    //         for (i = filters.length; i--;) {
    //             if (filters[i].getType() === 'category') {
    //                 value = filters[i].getValue();
    //                 if (value && window.categoriesUrls[value]) {
    //                     baseUrl = window.categoriesUrls[value];
    //                 }
    //             }
    //         }
    //     }
    //
    //     if (sortSelectElement) {
    //         if (sortSelectElement.value) {
    //             arguments['sort'] = sortSelectElement.value;
    //         }
    //     } else {
    //         // workaround for retaining order
    //         var currentUrl = document.location.href;
    //         var sortArgumentPosition = currentUrl.indexOf('sort:');
    //         if (sortArgumentPosition > 0) {
    //             var sortArgument = currentUrl.slice(sortArgumentPosition + 5);
    //             if (sortArgument.indexOf('/') > 0) {
    //                 sortArgument = sortArgument.slice(0, sortArgument.indexOf('/'));
    //             }
    //             if (sortArgument) {
    //                 arguments['sort'] = sortArgument;
    //             }
    //         }
    //     }
    //     baseUrl = baseUrl || window.productsListElementUrl;
    //     submitLocation = baseUrl + generateQueryString(arguments);
    //     //autoSubmit by default
    //     if (typeof window.productSearchLogics.useAutoSubmit == 'undefined' || window.productSearchLogics.useAutoSubmit()) {
    //         submitForm();
    //     }
    // };

    // var submitForm = function() {
    //     if (!submitLocation) {
    //         self.refresh();
    //     }
    //     document.location.href = submitLocation;
    // };
    //
    // var generateQueryString = function(arguments) {
    //     var queryString = '';
    //     for (var key in arguments) {
    //         if (arguments.hasOwnProperty(key)) {
    //             if (typeof arguments[key] != 'string') {
    //                 queryString += key + ':' + arguments[key].join(',') + '/';
    //             } else {
    //                 queryString += key + ':' + arguments[key] + '/';
    //             }
    //         }
    //     }
    //     return encodeURI(queryString);
    // };
    //
    // var onButtonClick = function(event) {
    //     eventsManager.preventDefaultAction(event);
    //     document.location.href = searchBaseUrl + generateQueryString();
    // };
    //
    // var getSelectElementValue = function(element) {
    //     if (element && element.options[element.selectedIndex].value) {
    //         return element.options[element.selectedIndex].value;
    //     }
    //     return '';
    // };
    init();
};