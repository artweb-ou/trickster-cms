window.ModalComponent = function(referralElement) {
	DomElementMakerMixin.call(this);

	var self = this;
	var modalContainer;
	var componentElement;
	var contentElement;
	var footerElement;
	var titleElement;
	var CLASS_OPEN = 'is_open';
	var UNDER_MODAL = 'under_modal';
	var MODAL_CONTAINER = 'modal_container';
	var displayed = false;

	var init = function() {
		var makeElement = self.makeElement;
		modalContainer = makeElement('div', MODAL_CONTAINER);

		componentElement = makeElement('div', 'modal', modalContainer);

		var element 		= makeElement('div', 'modal_header', componentElement);
		var element_content = makeElement('div', 'modal_content', componentElement);

		titleElement 	= makeElement('div', 'modal_title', element);
		contentElement 	= makeElement('div', 'modal_middle',element_content);

		var buttonElement = makeElement('div', 'modal_closebutton', element);
		eventsManager.addHandler(buttonElement, 'click', closeClick);
		footerElement = makeElement('div', 'modal_footer', componentElement);
	};
	var closeClick = function(event) {
		eventsManager.preventDefaultAction(event);
		self.setDisplayed(false);
	};
	this.addClass = function(newClass) {
		domHelper.addClass(componentElement, newClass);
	};
	this.removeClass = function(newClass) {
		domHelper.removeClass(componentElement, newClass);
	};
	this.setTitle = function(title) {
		titleElement.innerHTML = title;
	};
	this.setContent = function(content) {
		contentElement.innerHTML = content;
	};
	this.setControls = function(element) {
		while (footerElement.firstChild) {
			footerElement.removeChild(footerElement.firstChild);
		}
		footerElement.appendChild(element);
	};
	this.setDisplayed = function(newDisplayed) {
		if (displayed == newDisplayed) {
			return;
		}
		displayed = newDisplayed;
		if (displayed) {
			DarkLayerComponent.showLayer(closeClick, self.displayComponent, true); // onclickFunction, callback, allowClose
			domHelper.addClass(document.body, UNDER_MODAL);
			document.body.appendChild(modalContainer);
			self.addClass(CLASS_OPEN);
		} else {
			self.removeClass(CLASS_OPEN);
			DarkLayerComponent.forceHideLayer();
			domHelper.removeClass(document.body, UNDER_MODAL);
			document.body.removeChild(modalContainer);
		}
	};
	this.getDisplayed = function() {
		return displayed;
	};
	this.getComponentElement = function() {
		return componentElement;
	};

	init();
};
