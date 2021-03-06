window.BasketButtonComponent = function(componentElement, onClick, id) {
    var productId = null;
    var addToBasketButtonAction = window.addToBasketButtonAction;
    /*
        in settings in admin
        addToBasketButtonAction
        '0' => 'action_none',
        '1' => 'action_tooltip',
        '2' => 'action_modal',
    */

    var init = function() {
        if (id !== undefined) {
            productId = id;
        }
        eventsManager.addHandler(componentElement, 'click', clickHandler);
    };
    var clickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        eventsManager.cancelBubbling(event);
        registerEventHandlers();
        onClick();
    };
    var registerEventHandlers = function() {
        controller.addListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.addListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };
    var unRegisterEventHandlers = function() {
        controller.removeListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.removeListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };
    var shoppingBasketProductAdditionHandler = function() {
        unRegisterEventHandlers();

        domHelper.addClass(componentElement, 'basket_product_added');

        if (addToBasketButtonAction) {
            var message = [];
            var additionalContainerClassName = 'notice_box';
            var additionalClassName = 'notice_basket';
            var currentProduct = document.createElement('span');
            currentProduct.className = 'notice_product_name';
            if (typeof window.productDetailsData !== 'undefined') {
                currentProduct.textContent = window.productDetailsData.title || window.productDetailsData.title_ga;
            }
            var currentAmount = document.createElement('em');
            currentAmount.className = 'notice_product_amount';

            if (document.querySelector('.product_details_amount_input')) {
                currentAmount.textContent = document.querySelector('.product_details_amount_input').value;
            } else {
                currentAmount.textContent = 1;
            }

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
            bubbleText.innerHTML = window.translationsLogics.get('product.addedtobasket') + ' (' + currentAmount.outerHTML + ')';
            switch (addToBasketButtonAction) {
                case '1': // BubbleComponent
                    message['title'] = '';
                    // message['title'] = currentProduct.outerHTML;
                    message['content'] = bubbleText.outerHTML;
                    message['footer'] = seeBasket.outerHTML + continueShopping.outerHTML;

                    var bubbleComponent = new BubbleComponent(componentElement, message, additionalContainerClassName, additionalClassName, 'notice_continue_shopping', 2000);
                    bubbleComponent.start();
                    break;

                case '2': // ModalActionComponent
                    if (Array.isArray(window.productData)) {
                        message['title'] = window.productData[productId].name;
                    } else {
                        message['title'] = currentProduct.innerHTML;
                    }
                    message['content'] = bubbleText.innerHTML;
                    message['footer'] = seeBasket.outerHTML + continueShopping.outerHTML;

                    new ModalActionComponent(false, 'multiple', componentElement, additionalContainerClassName, 'notice_continue_shopping', message); // checkbox-input, multiple footer buttons, element for position, messages
                    break;
            }
        }
    };
    var shoppingBasketProductAddFailureHandler = function(argument) { // argument - is translation
        unRegisterEventHandlers();
        if (addToBasketButtonAction) {
            var message = [];
            var additionalContainerClassName = 'notice_box';
            message['title'] = window.productDetailsData.title || window.productDetailsData.title_ga;
            message['content'] = window.translationsLogics.get(argument);
            message['footer'] = '';
            // only modal on error
            new ModalActionComponent(false, false, componentElement, additionalContainerClassName, '', message); // checkbox-input, footer buttons, element for position, messages
            /*
                  switch(addToBasketButtonAction) {
                    case '1': // BubbleComponent
                      var bubbleComponent = new BubbleComponent(componentElement, message, additionalClassName, '', 3500);
                      bubbleComponent.start();
                      break;

                    case '2': // ModalActionComponent
                      new ModalActionComponent(false, false, componentElement, message); // checkbox-input, footer buttons, element for position, messages
                      break;
                  }
            */
        }
    };
    init();
};