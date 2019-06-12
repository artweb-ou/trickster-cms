window.bannerLogics = new function() {
    var initComponents = function() {
        var elements = _('.banner');
        for (var i = 0; i < elements.length; i++) {
            new BannerComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};