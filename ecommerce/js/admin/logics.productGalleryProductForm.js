window.productGalleryProductFormLogics = new function() {
    var initComponents = function() {
        var element = _('.productgalleryproduct_form')[0];
        if (element) {
            new ProductGalleryProductFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};