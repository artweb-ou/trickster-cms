window.EventsListFormComponent = function(componentElement) {
	var ajaxSelectElement;
	var periodSelectElement;
	var periodAutomaticSettingElements, periodManualSettingElements;
	var modeSelectElement;
	var modeAutomaticSettingElements, modeManualSettingElements;

	var init = function() {
		if (modeSelectElement = _('select.eventslist_mode_select', componentElement)[0]){
			modeAutomaticSettingElements = _('.eventslist_mode_auto_setting', componentElement);
			modeManualSettingElements = _('.eventslist_mode_manual_setting', componentElement);
			eventsManager.addHandler(modeSelectElement, "change", checkDisplay);
			checkDisplay();
		}
		if (ajaxSelectElement = _('select.eventslist_connected_events_select', componentElement)[0]){
			new AjaxSelectComponent(ajaxSelectElement, 'event', 'admin');
		}

	};
	var checkDisplay = function() {
		if (modeSelectElement.value == "auto") {
			for (var i = modeAutomaticSettingElements.length; i--;) {
				modeAutomaticSettingElements[i].style.display = "";
			}
			for (var i = modeManualSettingElements.length; i--;) {
				modeManualSettingElements[i].style.display = "none";
			}
		} else {
			for (var i = modeAutomaticSettingElements.length; i--;) {
				modeAutomaticSettingElements[i].style.display = "none";
			}
			for (var i = modeManualSettingElements.length; i--;) {
				modeManualSettingElements[i].style.display = "";
			}
		}
	};
	init();
};