window.GenericFormComponent = function(componentElement, index) {
	var self = this;
	this.componentElement = componentElement;

	var init = function() {
		if (index == 0) // 1st form element on the page
		{
			selectFirstInputElement();
		}
	};

	var selectFirstInputElement = function() {
		var formChildElements = _('*', componentElement);

		if (index != 0)   // lets not focus to anything
		{                 // if we're not the first form element...
			return;
		}

		for (var i = 0, l = formChildElements.length; i !== l; i++) {
			var formChildElement = formChildElements[i];

			if (formChildElement.tagName != "input" && formChildElement.type != "text") {
				if (formChildElement.tagName == "SELECT") {
					return;
				}
			} else {
				formChildElement.focus();
				var v = formChildElement.value;
				formChildElement.value = "";
				formChildElement.value = v;
				return;
			}
		}
	};

	init();
};