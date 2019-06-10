window.SelectedDiscountsComponent = function(componentElement) {
    var modeSelectElement;
    var automaticSettingElements, manualSettingElements;

    var init = function() {
        modeSelectElement = _('select.selecteddiscounts_mode_select', componentElement)[0];
        automaticSettingElements = _('.selecteddiscounts_auto_setting', componentElement);
        manualSettingElements = _('.selecteddiscounts_manual_setting', componentElement);
        checkMode();
        eventsManager.addHandler(modeSelectElement, 'change', checkMode);
    };
    var checkMode = function() {
        if (modeSelectElement.options[modeSelectElement.selectedIndex].value == 'auto') {
            for (var i = automaticSettingElements.length; i--;) {
                automaticSettingElements[i].style.display = '';
            }
            for (var i = manualSettingElements.length; i--;) {
                manualSettingElements[i].style.display = 'none';
            }
        } else {
            for (var i = automaticSettingElements.length; i--;) {
                automaticSettingElements[i].style.display = 'none';
            }
            for (var i = manualSettingElements.length; i--;) {
                manualSettingElements[i].style.display = '';
            }
        }
    };
    init();
};