window.BasketButtonComponent = function(componentElement, onClick) {
	let addToBasketButtonAction = window.addToBasketButtonAction;
	/*
addToBasketButtonAction
'0' => 'action_none',
'1' => 'action_tooltip',
'2' => 'action_modal',
	*/

	var init = function() {
		eventsManager.addHandler(componentElement, 'click', clickHandler);
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		registerEventHandlers();
		onClick();
	};
	var registerEventHandlers = function() {
		controller.addListener("shoppingBasketProductAdded", shoppingBasketProductAdditionHandler);
		controller.addListener("shoppingBasketProductAddFailure", shoppingBasketProductAddFailureHandler);
	};
	var unRegisterEventHandlers = function() {
		controller.removeListener("shoppingBasketProductAdded", shoppingBasketProductAdditionHandler);
		controller.removeListener("shoppingBasketProductAddFailure", shoppingBasketProductAddFailureHandler);
	};
	var shoppingBasketProductAdditionHandler = function() {
		unRegisterEventHandlers();

		domHelper.addClass(componentElement, 'basket_product_added')

		if(addToBasketButtonAction) {
			var message = [];
			let additionalClassName = 'notice_basket';
			var currentProduct = document.createElement('span');
			currentProduct.className = 'notice_product_name';
			currentProduct.textContent  = window.productDetailsData.name || window.productDetailsData.name_ga;

			var currentAmount = document.createElement('em');
			currentAmount.className = 'notice_product_amount';
			currentAmount.textContent = document.querySelector('.product_details_amount_input').value;

			var seeBasket = document.createElement('a');
			seeBasket.className = 'notice_see_basket button';
			seeBasket.setAttribute('href', window.shoppingBasketURL);
			seeBasket.textContent = translationsLogics.get('shoppingbasket.see_basket');

			var continueShopping = document.createElement('a');
			continueShopping.className = 'notice_continue_shopping button';
			continueShopping.setAttribute('href', window.shopLink);
			continueShopping.textContent = translationsLogics.get('shoppingbasket.continue_shopping');

			var bubbleText = document.createElement('span');
			bubbleText.className = 'notice_bubble_text';
			bubbleText.innerHTML  = window.translationsLogics.get('product.addedtobasket') + '('+currentAmount.outerHTML+')';

			switch(addToBasketButtonAction) {
				case '1': // BubbleComponent
					message['title'] = currentProduct.outerHTML;
					message['content'] = bubbleText.outerHTML;
					message['footer'] = seeBasket.outerHTML + continueShopping.outerHTML;

					var bubbleComponent = new BubbleComponent(componentElement, message, additionalClassName, 'notice_continue_shopping', 3500);
		bubbleComponent.start();
					break;

				case '2': // ModalActionComponent
					message['title'] = currentProduct.innerHTML;
					message['content'] = bubbleText.innerHTML;
					message['footer'] = seeBasket.outerHTML + continueShopping.outerHTML;

					new ModalActionComponent(false, 'multiple', componentElement, message); // checkbox-input, multiple footer buttons, element for position, messages
					break;
			}
		}
	};
	var shoppingBasketProductAddFailureHandler = function() {
		unRegisterEventHandlers();
		if(addToBasketButtonAction) {
			var message = [];
			let additionalClassName = 'notice_basket';
			message['title'] = window.productDetailsData.name || window.productDetailsData.name_ga;
			message['content'] = window.translationsLogics.get('product.quantityunavailable');
			message['footer'] = '';
// only modal on error
			new ModalActionComponent(false, false, componentElement, message); // checkbox-input, footer buttons, element for position, messages
/*
			switch(addToBasketButtonAction) {
				case '1': // BubbleComponent
					var bubbleComponent = new BubbleComponent(componentElement, message, additionalClassName, '', 3500);
					bubbleComponent.start();
					break;

				case '2': // ModalActionComponent
					break;
			}
*/
		}
	};
	init();
};