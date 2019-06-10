window.socialPostLogics = new function() {
    var initComponents = function() {
        var elements = _('.socialpost_form');
        for (var i = 0; i < elements.length; i++) {
            new SocialPostFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};