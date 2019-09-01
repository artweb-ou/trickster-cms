window.autocompleteOptionsLogics  = new function() {
    var initComponents = function() {
        var formElement = _('.input_element_form')[0];
        var autocompleteElement;
        if (formElement) {
            autocompleteElement = formElement.querySelector('select.autocomplete_options');
            if(autocompleteElement) {
                new autocompleteOptionsComponent(formElement, autocompleteElement);
            }
        }
    };
    controller.addListener('initDom', initComponents);
};