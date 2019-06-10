window.latestNewsFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.latest_news_modify_block');
        for (var i = 0; i < elements.length; i++) {
            new LatestNewsFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};