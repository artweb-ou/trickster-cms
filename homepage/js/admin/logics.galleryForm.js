window.galleryFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.gallery_form');
        for (var i = 0; i < elements.length; i++) {
            new GalleryFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};