window.contentTogglerLogics = new function() {
    var initComponents = function() {
        var elements = _('.toggleable_component');
        for (var i = 0; i < elements.length; i++) {
            new ToggleableContainer(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};