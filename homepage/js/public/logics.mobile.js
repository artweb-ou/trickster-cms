window.mobileLogics = new function() {
	var menu;
	var self = this;

	var initComponents = function() {
		var headerElement = document.querySelector('.mobileheader');
		if (headerElement) {
			new MobileHeaderComponent(headerElement);

			var menuElement = headerElement.querySelector('.mobilemenu');
			if (menuElement) {
				document.body.appendChild(menuElement);
				menu = new MobileMenuComponent(menuElement);
			}
		}
	};
	this.toggleMenu = function() {
		if (menu) {
			menu.toggleVisibility();
		}
	};
	controller.addListener('initDom', initComponents);
};