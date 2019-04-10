window.BasketButtonComponent = function(componentElement, onClick) {
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
		var bubbleComponent = new BubbleComponent(componentElement, window.translationsLogics.get('product.addedtobasket'));
		bubbleComponent.start();
	};
	var shoppingBasketProductAddFailureHandler = function() {
		unRegisterEventHandlers();
		alert(window.translationsLogics.get('product.quantityunavailable'));
	};
	init();
};