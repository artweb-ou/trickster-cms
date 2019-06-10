window.categoryDetailsLogics = new function() {
    var initComponents = function() {
        var elements = _('.category_thumbnail_block');
        for (var i = 0; i < elements.length; i++) {
            new CategoryThumbnailComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};