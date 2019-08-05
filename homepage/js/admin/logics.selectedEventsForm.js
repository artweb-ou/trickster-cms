window.SelectedEventsFormLogics = new function() {
    var initComponents = function() {
        var element = _('.selectedevents_form')[0];
        if (element) {
            new SelectedEventsFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};