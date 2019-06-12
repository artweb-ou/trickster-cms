window.EventFormComponent = function(componentElement) {
    var ajaxSelectElement;

    var init = function() {
        if (ajaxSelectElement = _('select.event_connected_eventslists_select', componentElement)[0]) {
            new AjaxSelectComponent(ajaxSelectElement, 'eventsList', 'admin');
        }
    };

    init();
};