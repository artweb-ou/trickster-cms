window.ProductsFilterComponent = function(componentElement, listComponent) {
    var filters = [];
    var self = this;
    var filtersData = [];

    var init = function() {

    };
    this.updateData = function(newData) {
        filtersData = newData;
    };

    this.initFilters = function() {
        var element, i;
        for (i = 0; i < filtersData.length; i++) {
            if (element = componentElement.querySelector('select.products_filter_dropdown.products_filter_' + filtersData[i].id)) {
                var filter = new ProductsDropdownFilterComponent(element, filtersData[i], self, listComponent);
                filters.push(filter);
            }
        }


        // elements = componentElement.querySelectorAll('input.products_filter_radio');
        // for (i = elements.length; i--;) {
        //     filters.push(new ProductsRadioFilterComponent(elements[i], self.refresh));
        // }
    };
    this.refresh = function() {
        // var arguments = {};
        // for (var i = filters.length; i--;) {
        //     filters[i].modifyFilterArguments(arguments);
        // }
        // document.location.href = window.currentElementURL + generateQueryString(arguments);
    };

    // var generateQueryString = function(arguments) {
    //     var queryString = '';
    //     for (var key in arguments) {
    //         queryString += key + ':' + arguments[key].join(',') + '/';
    //     }
    //     // workaround for retaining order
    //     var currentUrl = document.location.href;
    //     var sortArgumentPosition = currentUrl.indexOf('sort:');
    //     if (sortArgumentPosition > 0) {
    //         var sortSlice = currentUrl.slice(sortArgumentPosition);
    //         if (sortSlice.indexOf('limit:') <= 0) {
    //             var limitArgumentPosition = currentUrl.indexOf('limit:');
    //             if (limitArgumentPosition > 0) {
    //                 sortSlice = currentUrl.slice(limitArgumentPosition);
    //             }
    //         }
    //         queryString += sortSlice;
    //     }
    //     return encodeURI(queryString);
    // };

    init();
};

window.ProductsDropdownFilterComponent = function(componentElement, filterData, parentComponent, listComponent) {
    var init = function() {
        if (componentElement) {

        }
        eventsManager.addHandler(componentElement, 'change', change);
    };

    var change = function(event) {
        listComponent.changeFilterValue(filterData.id, getValue());
    };

    // this.modifyFilterArguments = function(arguments) {
    //     var myValue;
    //     if (myValue = self.getValue()) {
    //         if (typeof arguments[type] == 'undefined') {
    //             arguments[type] = [];
    //         }
    //         arguments[type][arguments[type].length] = myValue;
    //     }
    // };

    var getValue = function() {
        if (componentElement.options && componentElement.options[componentElement.selectedIndex].value) {
            return componentElement.options[componentElement.selectedIndex].value;
        }
        return '';
    };

    init();
};

// window.ProductsRadioFilterComponent = function(componentElement, onChange) {
//     var type;
//     var self = this;
//
//     var init = function() {
//         type = componentElement.className.slice(componentElement.className.indexOf('products_filter_radio_type_') + 27);
//         if (type.indexOf(' ') >= 0) {
//             type = type.slice(0, type.indexOf(' '));
//         }
//         eventsManager.addHandler(componentElement, 'change', change);
//     };
//
//     var change = function(event) {
//         onChange(self);
//     };
//
//     this.modifyFilterArguments = function(arguments) {
//         var myValue;
//         if (myValue = self.getValue()) {
//             if (typeof arguments[type] == 'undefined') {
//                 arguments[type] = [];
//             }
//             arguments[type][arguments[type].length] = myValue;
//         }
//     };
//
//     this.getValue = function() {
//         if (componentElement.checked) {
//             return componentElement.value;
//         }
//         return '';
//     };
//
//     this.getType = function() {
//         return type;
//     };
//     init();
// };

// window.ProductsCheckboxesFilterComponent = function(componentElement, onChange) {
//     var type;
//     var checkboxElements = [];
//     var hidden = false;
//     var self = this;
//
//     var init = function() {
//         var titleElement = _('.productsearch_field_label', componentElement)[0];
//         type = componentElement.className.slice(componentElement.className.indexOf('products_filter_type_') + 21);
//         if (type.indexOf(' ') >= 0) {
//             type = type.slice(0, type.indexOf(' '));
//         }
//         checkboxElements = _('input.products_filter_checkbox', componentElement);
//         eventsManager.addHandler(componentElement, 'change', change);
//     };
//
//     var change = function(event) {
//         onChange(self);
//     };
//
//     this.modifyFilterArguments = function(arguments) {
//         var values = self.getValues();
//         if (values.length > 0) {
//             if (typeof arguments[type] == 'undefined') {
//                 arguments[type] = [];
//             }
//             for (var i = 0; i != values.length; ++i) {
//                 arguments[type].push(values[i]);
//             }
//         }
//     };
//
//     this.getValues = function() {
//         var values = [];
//         for (var i = 0; i != checkboxElements.length; ++i) {
//             if (checkboxElements[i].checked) {
//                 values[values.length] = checkboxElements[i].value;
//             }
//         }
//         return values;
//     };
//
//     this.getType = function() {
//         return type;
//     };
//     init();
// };