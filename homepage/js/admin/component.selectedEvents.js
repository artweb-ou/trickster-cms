window.SelectedEventsComponent = function(componentElement) {
	var modeSelectElement;
	var automaticSettingElements, manualSettingElements;

	var init = function() {
		modeSelectElement = _('select.selectedevents_mode_select', componentElement)[0];
		automaticSettingElements = _('.selectedevents_auto_setting', componentElement);
		manualSettingElements = _('.selectedevents_manual_setting', componentElement);
		checkMode();
		eventsManager.addHandler(modeSelectElement, "change", checkMode);
		new AjaxSelectComponent(_('select.selectedevents_connected_events_select', componentElement)[0], 'event', 'admin');
		new AjaxSelectComponent(_('select.selectedevents_connected_eventslists_select', componentElement)[0], 'eventsList', 'admin');
	};
	var checkMode = function() {
		if (modeSelectElement.options[modeSelectElement.selectedIndex].value == "auto") {
			for (var i = automaticSettingElements.length; i--;) {
				automaticSettingElements[i].style.display = "";
			}
			for (var i = manualSettingElements.length; i--;) {
				manualSettingElements[i].style.display = "none";
			}
		} else {
			for (var i = automaticSettingElements.length; i--;) {
				automaticSettingElements[i].style.display = "none";
			}
			for (var i = manualSettingElements.length; i--;) {
				manualSettingElements[i].style.display = "";
			}
		}
	};
	init();
};