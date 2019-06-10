window.eventsListFormLogics = new function() {
    var initComponents = function() {
        var element = _('.eventslist_form')[0];
        if (element) {
            new EventsListFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};