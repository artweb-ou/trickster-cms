window.brandsListFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.brandslist');
        for (var i = elements.length; i--;) {
            new BrandsListFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};