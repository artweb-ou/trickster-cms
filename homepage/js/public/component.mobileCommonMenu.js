window.MobileCommonMenuComponent = function(buttonElement) {
	var contentElement;
	var parentElement;
	var menuElement;
	var menuContainer;
	var closeElement;
	var additionalCloseElements;
	var menu;
	var menuHeaderElement;
	var self = this;

	var init = function() {
		var menuId = buttonElement.dataset.menuid;
		if (menuId) {
			contentElement = document.querySelector(menuId);
			if (contentElement) {
				parentElement = contentElement.parentNode;
				menuContainer = document.createElement('div');
				menuContainer.className = 'mobilemenu';
				if(buttonElement.dataset.menuclass) {
					domHelper.addClass(menuContainer, buttonElement.dataset.menuclass)
				}
				document.body.appendChild(menuContainer);

				menuElement = document.createElement('div');
				menuElement.className = 'mobilemenu_main';
				menuContainer.appendChild(menuElement);

				menuHeaderElement = document.createElement('div');
				menuHeaderElement.className = 'mobilemenu_header';
				menuElement.appendChild(menuHeaderElement);

				closeElement = document.createElement('div');
				closeElement.className = 'mobilemenu_closeicon';
				menuHeaderElement.appendChild(closeElement);

				additionalCloseElements = contentElement.querySelectorAll('.mobile_common_menu_close');

				menu = new MobileMenuComponent(menuContainer, toggleStartCallback);

				for (var i = 0; i < additionalCloseElements.length; ++i) {
					eventsManager.addHandler(additionalCloseElements[i], 'click', toggleMenu);
				}
				eventsManager.addHandler(buttonElement, 'click', toggleMenu);
			}
		}
	};

	var toggleMenu = function() {
		menu.toggleVisibility();
	};

	var toggleStartCallback = function() {
		if(menu.isVisible()) {
			parentElement.appendChild(contentElement);
		}else {
			menuElement.appendChild(contentElement);
		}
		controller.fireEvent('MobileCommonMenuReappended');
	};

	init();
};