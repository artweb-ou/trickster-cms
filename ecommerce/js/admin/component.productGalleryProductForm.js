window.ProductGalleryProductFormComponent = function(componentElement) {
    var init = function() {
        var element;
        if (element = _('.productgalleryproduct_form_productselect', componentElement)[0]) {
            new AjaxSelectComponent(element, 'product', 'admin');
        }
    };
    init();
};