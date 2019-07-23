window.EventsListFormComponent = function(componentElement) {
    var ajaxSelectElement;
    var periodSelectElement;
    var periodAutomaticSettingElements, periodManualSettingElements;
    var modeSelectElement;
    var modeAutomaticSettingElements, modeManualSettingElements;

    var init = function() {
        if (modeSelectElement = componentElement.querySelector('select.eventslist_mode_select')) {
            modeAutomaticSettingElements = componentElement.querySelectorAll('.eventslist_mode_auto_setting');
            modeManualSettingElements = componentElement.querySelectorAll('.eventslist_mode_manual_setting');
            eventsManager.addHandler(modeSelectElement, 'change', checkDisplay);
            checkDisplay();
        }
        if (ajaxSelectElement = componentElement.querySelector('select.eventslist_connected_events_select', componentElement)) {
            new AjaxSelectComponent(ajaxSelectElement, 'event', 'admin');
        }

    };
    var checkDisplay = function() {
        var i;
        if (modeSelectElement.value === 'auto') {
            for (i = modeAutomaticSettingElements.length; i--;) {
                modeAutomaticSettingElements[i].style.display = '';
            }
            for (i = modeManualSettingElements.length; i--;) {
                modeManualSettingElements[i].style.display = 'none';
            }
        } else {
            for (i = modeAutomaticSettingElements.length; i--;) {
                modeAutomaticSettingElements[i].style.display = 'none';
            }
            for (i = modeManualSettingElements.length; i--;) {
                modeManualSettingElements[i].style.display = '';
            }
        }
    };
    init();
};