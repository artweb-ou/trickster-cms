window.selectedDiscountsLogics = new function() {
    var initComponents = function() {
        var elements = _('.selecteddiscounts_form');
        for (var i = 0; i < elements.length; i++) {
            new SelectedDiscountsComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};