window.SubMenuItemComponent = function(componentElement) {
	var self = this;
	var verticalPopup = false;
	var menuInfo = false;
	var popupObject = false;
	this.id = false;
	var displayDelayTimeout;

	var init = function() {
		self.id = parseInt(componentElement.className.split('menuid_')[1], 10);
		if (componentElement.className.indexOf('vertical_popup') >= 0) {
			verticalPopup = true;
		}
		if (menuInfo = window.subMenuLogics.getMenuInfo(self.id)) {
			var subMenuList = window.subMenuLogics.getSubMenuInfo(self.id);
			if (subMenuList.length > 0) {

				popupObject = new SubmenuItemPopupComponent(subMenuList, self, componentElement);
			}
			window.eventsManager.addHandler(componentElement, 'mouseenter', self.mouseEnterHandler);
			window.eventsManager.addHandler(componentElement, 'mouseleave', self.mouseLeaveHandler);
		}
	};

	this.mouseEnterHandler = function() {
		domHelper.addClass(componentElement, 'menuitem_hover');
		if (popupObject) {
			displayDelayTimeout = window.setTimeout(function() {
				popupObject.displayComponent();
			}, 150);
		}
	};

	this.mouseLeaveHandler = function(event) {
		window.clearTimeout(displayDelayTimeout);

		if (popupObject) {
			var hidingRequired = true;

			if (typeof event.relatedTarget == 'undefined' && typeof event.toElement == 'object') {
				event.relatedTarget = event.toElement;
			}

			if (event.relatedTarget) {
				if (event.relatedTarget == popupObject.componentElement || domHelper.isAChildOf(popupObject.componentElement, event.relatedTarget)) {
					hidingRequired = false;
				} else if (event.relatedTarget == componentElement || domHelper.isAChildOf(componentElement, event.relatedTarget)) {
					hidingRequired = false;
				}
			}
			if (hidingRequired) {
				popupObject.attemptHideComponent();
			}
		}
		domHelper.removeClass(componentElement, 'menuitem_hover');
	};
	this.isVerticalPopup = function() {
		return verticalPopup;
	};
	init();
};
window.SubmenuItemPopupComponent = function(subMenusList, menuComponent, referenceElement) {
	var self = this;
	var componentElement;
	var contentElement;
	var displayed = false;
	var hideTimeout = false;
	var hovered = false;
	var attached = false;

	this.componentElement = false;

	var createDomStructure = function() {
		componentElement = document.createElement('div');

		componentElement.className = 'submenuitem_popup_block';
		componentElement.style.opacity = '0';

		contentElement = document.createElement('div');
		contentElement.className = 'submenuitem_popup_content';
		componentElement.appendChild(contentElement);

		for (var i = 0; i < subMenusList.length; i++) {
			var subMenu = new SubMenusPopupItemComponent(subMenusList[i]);
			contentElement.appendChild(subMenu.componentElement);
		}
		self.componentElement = componentElement;
		window.eventsManager.addHandler(componentElement, 'mouseenter', onMouseEnter);
		window.eventsManager.addHandler(componentElement, 'mouseleave', onMouseLeave);
	};
	var attach = function() {
		if (!attached) {
			if (!componentElement) {
				createDomStructure();
			}
			attached = true;
			document.body.appendChild(componentElement);
		}
	};
	var detach = function() {
		if (attached) {
			attached = false;
			document.body.removeChild(componentElement);
		}
	};
	this.displayComponent = function() {
		window.clearTimeout(hideTimeout);
		var positions;
		if (!displayed) {
			displayed = true;
			attach();
			if (menuComponent.isVerticalPopup()) {
				var menuWidth = referenceElement.offsetWidth;
				var menuHeight = referenceElement.offsetHeight;

				componentElement.style.display = 'block';
				if (componentElement.offsetWidth < menuWidth) {
					componentElement.style.minWidth = menuWidth + 'px';
				}

				positions = window.domHelper.getElementPositions(referenceElement);
				componentElement.style.left = positions.x + 'px';
				componentElement.style.top = (positions.y + menuHeight) + 'px';

				TweenLite.to(componentElement, 0.25, {'css': {'opacity': 1}});
			} else {
				componentElement.style.display = 'block';
				positions = window.domHelper.getElementPositions(referenceElement);

				componentElement.style.left = positions.x + referenceElement.offsetWidth + 'px';
				componentElement.style.top = positions.y + 'px';

				TweenLite.to(componentElement, 0.25, {'css': {'opacity': 1}});
			}
		}
	};
	this.attemptHideComponent = function() {
		hideTimeout = window.setTimeout(startHideComponent, 250);
	};
	var startHideComponent = function() {
		if (!hovered) {
			displayed = false;
			if (componentElement) {
				TweenLite.to(componentElement, 0.1, {'css': {'opacity': 0}, 'onComplete': hideComponent});
			}
		}
	};
	var hideComponent = function() {
		if (!displayed) {
			if (componentElement) {
				componentElement.style.display = 'none';
				detach();
			}
		}
	};

	var onMouseEnter = function(event) {
		hovered = true;
		menuComponent.mouseEnterHandler(event);
	};
	var onMouseLeave = function(event) {
		hovered = false;
		menuComponent.mouseLeaveHandler(event);
	};
};
window.SubMenusPopupItemComponent = function(menuInfo) {
	var self = this;
	this.componentElement = false;

	var init = function() {
		self.componentElement = document.createElement('a');
		self.componentElement.href = menuInfo.URL;
		self.componentElement.className = 'submenuitem_popup_item';

		var subElement1 = document.createElement('div');
		subElement1.className = 'submenuitem_popup_item_left';
		self.componentElement.appendChild(subElement1);
		var subElement2 = document.createElement('div');
		subElement2.className = 'submenuitem_popup_item_center';
		self.componentElement.appendChild(subElement2);
		var contentElement = document.createElement('div');
		contentElement.className = 'submenuitem_popup_item_content';
		self.componentElement.appendChild(contentElement);

		contentElement.innerHTML = menuInfo.title;
	};
	init();
};