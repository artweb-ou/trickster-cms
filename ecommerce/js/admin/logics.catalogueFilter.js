window.catalogueFilterLogics = new function() {
    var initComponents = function() {
        var elements = _('.catalogue_filter');
        for (var i = elements.length; i--;) {
            new CatalogueFilterComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};