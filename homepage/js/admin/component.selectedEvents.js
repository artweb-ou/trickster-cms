window.SelectedEventsComponent = function(componentElement) {
    var modeSelectElement;
    var automaticSettingElements, manualSettingElements;

    var init = function() {
        modeSelectElement = componentElement.querySelector('select.selectedevents_mode_select');
        automaticSettingElements = componentElement.querySelectorAll('.selectedevents_auto_setting');
        manualSettingElements = componentElement.querySelectorAll('.selectedevents_manual_setting');
        checkMode();
        eventsManager.addHandler(modeSelectElement, 'change', checkMode);
        new AjaxSelectComponent(componentElement.querySelector('select.selectedevents_connected_events_select'), 'event', 'admin');
        new AjaxSelectComponent(componentElement.querySelector('select.selectedevents_connected_eventslists_select'), 'eventsList', 'admin');
    };
    var checkMode = function() {
        var i;
        if (modeSelectElement.options[modeSelectElement.selectedIndex].value === 'auto') {
            for (i = automaticSettingElements.length; i--;) {
                automaticSettingElements[i].style.display = '';
            }
            for (i = manualSettingElements.length; i--;) {
                manualSettingElements[i].style.display = 'none';
            }
        } else {
            for (i = automaticSettingElements.length; i--;) {
                automaticSettingElements[i].style.display = 'none';
            }
            for (i = manualSettingElements.length; i--;) {
                manualSettingElements[i].style.display = '';
            }
        }
    };
    init();
};