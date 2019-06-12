window.ProductImportFormLogics = new function() {
    var initComponents = function() {
        var element = _('.productimport_form')[0];
        if (element) {
            new ProductImportFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};