window.selectedEventsLogics = new function() {
    var initComponents = function() {
        var elements = _('.selectedevents_form');
        for (var i = 0; i < elements.length; i++) {
            new SelectedEventsComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};