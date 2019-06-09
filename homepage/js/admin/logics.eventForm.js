window.eventFormLogics = new function() {
    var initComponents = function() {
        var element = _('.event_form')[0];
        if (element) {
            new EventFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};