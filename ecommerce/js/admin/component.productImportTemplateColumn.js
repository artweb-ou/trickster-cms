window.ProductImportTemplateColumnComponent = function(componentElement) {
    var variableSelectElement;
    var parameterSearchElement;
    var parameterSearchContainerElement;
    var init = function() {
        variableSelectElement = _('select.productimporttemplatecolumn_form_variable_select', componentElement)[0];
        eventsManager.addHandler(variableSelectElement, 'change', checkVariableSelect);

        parameterSearchContainerElement = _('.productimporttemplatecolumn_form_parametersearch_container', componentElement)[0];
        parameterSearchElement = _('.productimporttemplatecolumn_form_parametersearch', componentElement)[0];
        new AjaxSelectComponent(parameterSearchElement, 'productParameter,productSelection,productSelectionValue',
            'admin');

        checkVariableSelect();
    };

    var checkVariableSelect = function() {
        if (variableSelectElement.options[variableSelectElement.selectedIndex].value != 'parameter') {
            parameterSearchContainerElement.style.display = 'none';
        } else {
            parameterSearchContainerElement.style.display = '';
        }
    };
    init();
};