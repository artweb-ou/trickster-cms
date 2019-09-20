window.dropdownShowHideFields = new function() {
	let initComponents = function() {
		let element = document.querySelector('.dropdown_show_hide_fields');
		if (element) {
			new DropdownShowHideFields(element);
		}
	};
	controller.addListener('initDom', initComponents);
};