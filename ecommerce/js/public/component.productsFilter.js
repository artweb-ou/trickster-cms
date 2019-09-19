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
            if (element = componentElement.querySelector('.products_filter_item.products_filter_' + filtersData[i].getId())) {
                var filter = new ProductsDropdownFilterComponent(element, filtersData[i], self, listComponent);
                filters.push(filter);
            }
        }

        // elements = componentElement.querySelectorAll('input.products_filter_radio');
        // for (i = elements.length; i--;) {
        //     filters.push(new ProductsRadioFilterComponent(elements[i], self.refresh));
        // }
    };
    this.rebuildFilters = function() {
        while (componentElement.firstChild) {
            componentElement.removeChild(componentElement.firstChild);
        }
        filters = [];
        var html = '';
        for (var i = 0; i < filtersData.length; i++) {
            var data = {
                'filter': filtersData[i],
            };
            html += smartyRenderer.fetch('component.filterdropdown.tpl', data);
        }
        componentElement.innerHTML = html;
        dropDownManager.initDropdowns(componentElement);
        self.initFilters();
    };

    init();
};

window.ProductsDropdownFilterComponent = function(componentElement, filterData, parentComponent, listComponent) {
    var selectElement;
    var init = function() {
        if (!componentElement) {

            // <div class="products_filter_item"><div class="products_filter_label">
            //         Kõik kaubamärgid:
            //         </div><select autocomplete="off" class="products_filter_dropdown products_filter_dropdown_type_brand products_filter_brand dropdown_placeholder" style="display: none;"><option value="">Kõik </option><option value="5279">
            //         Holden Decor Ltd.
            //     </option></select></div>
        } else {
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