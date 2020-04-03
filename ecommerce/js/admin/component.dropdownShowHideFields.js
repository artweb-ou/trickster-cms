window.DropdownShowHideFields = function(componentElement) {
	let placeholder;
	let selectedOption;
	let fields = [];

	let init = function() {
		placeholder = componentElement.querySelector('.dropdown_placeholder');
		if(placeholder) {
			selectedOption = placeholder.selectedIndex;
			setFields();
		}
		if(placeholder && fields) {
			setHandlers();
			fieldsHandler();
		}
	};

	let setFields = function() {
		let formFields = componentElement.querySelectorAll('.dropdown_fields');
		for(let i = 0; i < placeholder.options.length; i++) {
			fields[i] = [];
			for(let a = 0; a < formFields.length; a++) {
				if(formFields[a].classList.contains(placeholder.options[i].value)) {
					fields[i].push(formFields[a]);
				}
			}
		}
	};

	let setHandlers = function() {
		eventsManager.addHandler(placeholder, 'change', dropdownHandler);
	};

	let dropdownHandler = function() {
		selectedOption = placeholder.selectedIndex;
		fieldsHandler();
	};

	let fieldsHandler = function() {
		for(let i = 0; i < fields.length; i++) {
			for (let a = 0; a < fields[i].length; a++) {
				hideField(fields[i][a]);
			}
		}
		for(let a = 0; a < fields[selectedOption].length; a++) {
			showField(fields[selectedOption][a]);
		}
	};

	let showField = function(element) {
		element.style.opacity = 1;
		element.style.visibility = 'visible';
		element.style.display = 'table-row';
	};

	let hideField = function(element) {
		element.style.opacity = 0;
		element.style.visibility = 'hidden';
		element.style.display = 'none';
	};

	init();
};