window.MobileCommonMenuLogics = new function() {
	var initComponents = function() {
		var buttonElements = document.querySelectorAll('.mobile_common_menu_button');
		for (var i = 0; i < buttonElements.length; i++) {
			new MobileCommonMenuComponent(buttonElements[i]);
		}
	};

	controller.addListener('initDom', initComponents);
};