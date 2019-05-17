window.MobileCommonMenuComponent = function(buttonElement) {
	var contentElement;
	var parentElement;
	var menuElement;
	var menuContainer;
	var closeElement;
	var menu;
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

				closeElement = document.createElement('div');
				closeElement.className = 'mobilemenu_closeicon';
				menuContainer.appendChild(closeElement);

				menu = new MobileMenuComponent(menuContainer, toggleStartCallback);

				eventsManager.addHandler(closeElement, 'click', menu.toggleVisibility);
				eventsManager.addHandler(buttonElement, 'click', menu.toggleVisibility);
			}
		}
	};

	var toggleStartCallback = function() {
		if(menu.isVisible()) {
			parentElement.appendChild(contentElement);
		}else {
			menuElement.appendChild(contentElement);
		}
	};

	init();
};