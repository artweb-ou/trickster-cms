window.ProductCatalogueFormComponent = function(componentElement) {
    var categorizeCheckboxElement;
    var categoriesRowElement;
    var populationRowElement;

    var init = function() {
        categorizeCheckboxElement = _('.checkbox_placeholder', componentElement)[0];
        categoriesRowElement = _('.productcatalogue_form_check_row_categories', componentElement)[0];
        populationRowElement = _('.productcatalogue_form_check_row_population', componentElement)[0];
        eventsManager.addHandler(categorizeCheckboxElement, 'change', refresh);
        refresh();
    };

    var refresh = function() {
        if (categorizeCheckboxElement.checked) {
            if (categoriesRowElement) {
                categoriesRowElement.style.display = '';
            }
            if (categoriesRowElement) {
                populationRowElement.style.display = '';
            }
        } else {
            if (categoriesRowElement) {
                categoriesRowElement.style.display = 'none';
            }
            if (categoriesRowElement) {
                populationRowElement.style.display = 'none';
            }
        }
    };
    init();
};