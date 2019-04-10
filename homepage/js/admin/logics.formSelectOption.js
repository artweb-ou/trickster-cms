window.formSelectOptionLogics = new function() {
    var initComponents = function() {
        var elements = _('.form_select_option_form');
        for (var i = elements.length; i--;) {
            new FormSelectOptionFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};