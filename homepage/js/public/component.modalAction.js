window.ModalActionComponent = function(checkboxElement, footerElement, elementForPosition, message) {
	//	ModalComponent.call(this);
	var self = this;
	var html= document.documentElement;
	var htmlScrollTopStart;
	var selfModalComponent = new ModalComponent(elementForPosition);

	var init = function() {
		var makeElement = selfModalComponent.makeElement;
		selfModalComponent.addClass('modal-buttons');
		selfModalComponent.setTitle(message['title']);
		selfModalComponent.setContent(message['content']);

		var fragment = document.createDocumentFragment();
		var resetButtonElement, submitButtonElement, footerElements;
/*
		resetButtonElement = makeElement('div', 'cancel button button_outlined', fragment);
		resetButtonElement.innerHTML = translationsLogics.get('product.quantityunavailable');
		eventsManager.addHandler(resetButtonElement, 'click', resetClick);
*/
		if(checkboxElement){
			submitButtonElement = makeElement('div', 'submit button', fragment);
			submitButtonElement.innerHTML = message['footer'];
			// htmlScrollTopStart = html.scrollTop;
			eventsManager.addHandler(submitButtonElement, 'click', submitClick);
		}
		else if(footerElement === 'multiple') {
			footerElements = makeElement('div', 'footer buttons', fragment);
			footerElements.innerHTML = message['footer'];
		}

		selfModalComponent.setControls(fragment);
		selfModalComponent.setDisplayed(true);
	};
	var submitClick = function(event) {
		eventsManager.preventDefaultAction(event);
		checkboxElement.checked = !checkboxElement.checked;
		window.eventsManager.fireEvent(checkboxElement, 'change');
		var formElement = checkboxElement.form;
		selfModalComponent.setDisplayed(false);

		formElement.submit();
		// html.scrollTop = htmlScrollTopStart;
	};


/*
	var resetClick = function(event) {
		eventsManager.preventDefaultAction(event);
		self.setDisplayed(false);
		return false;
	};
*/
	init();
};