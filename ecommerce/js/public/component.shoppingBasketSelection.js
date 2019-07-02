window.ShoppingBasketSelectionComponent = function(componentElement) {
    var conditionsContentElement;
    var conditionsTextElement;
    var submitButtonElement;
    var formElement;
    var contentsElement;
    var messageElement;
    var productPriceElements;
    var totalsContainerElement;
    var footerTotalsContainerElement;
    var totalsComponent;
    var showInBasketDiscountsComponent;
    var conditionsCheckboxInput;
    var basketCells = [];

    var init = function() {
        contentsElement = _('.shoppingbasket_contents', componentElement)[0];
        if (contentsElement) {
            conditionsContentElement = _('.shoppingbasket_form_conditions_content', formElement)[0];
            conditionsTextElement = _('.shoppingbasket_form_conditions_text', conditionsContentElement)[0];
        }
        if (formElement = componentElement.querySelector('.shoppingbasket_form', componentElement)) {
            if (submitButtonElement = componentElement.querySelector('.shoppingbasket_form_submit')) {
                eventsManager.addHandler(submitButtonElement, 'click', submitForm);
            }
        }

        messageElement = _('.shoppingbasket_selection_message', componentElement)[0];
        var element = _('.shoppingbasket_products', componentElement)[0];
        if (element) {
            new ShoppingBasketSelectionProducts(element);
        }
        totalsContainerElement = componentElement.querySelector('.shoppingbasket_total_products');
        productPriceElements = componentElement.querySelectorAll('.shoppingbasket_total_value');

        if (element = componentElement.querySelector('.shoppingbasket_services_component')) {
            new ShoppingBasketSelectionServices(element);
        }
        element = _('.shoppingbasket_form_block', componentElement)[0];
        if (element) {
            new ShoppingBasketSelectionForm(element, formElement);
        }

        element = _('.shoppingbasket_promocode', componentElement)[0];
        if (element) {
            new ShoppingBasketPromoCodeComponent(element);
        }

        footerTotalsContainerElement = _('.shoppingbasket_totals_table', componentElement)[0];

        if (footerTotalsContainerElement) {
            var element2 = _('.shoppingbasket_totals', componentElement)[0];
            if (element2) {
                totalsComponent = new ShoppingBasketTotalsComponent(element2);
            }
        }

        conditionsCheckboxInput = componentElement.querySelector('#shoppingbasket_form_conditions_checkbox');

        showInBasketDiscountsComponent = _('.shoppingbasket_discounts', componentElement)[0];
        shoppingBasketLogics.trackCheckout();
        controller.addListener('startApplication', startApplication);
        controller.addListener('shoppingBasketUpdated', updateData);
    };
    var startApplication = function() {
        updateData();
    };
    var updateData = function() {
        if (messageElement) {
            messageElement.innerHTML = window.shoppingBasketLogics.message;
        }
        if (conditionsContentElement && conditionsTextElement) {
            var selectedCountry = shoppingBasketLogics.getSelectedCountry();
            if (selectedCountry && selectedCountry.conditionsText) {
                conditionsContentElement.style.display = 'block';
                conditionsTextElement.innerHTML = selectedCountry.conditionsText;
            } else {
                conditionsContentElement.style.display = '';
            }
        }
        var products = window.shoppingBasketLogics.productsList;
        if (products.length > 0) {
            for (var i = 0; i < productPriceElements.length; i++) {
                productPriceElements[i].innerHTML = shoppingBasketLogics.productsSalesPrice + ' ' + window.selectedCurrencyItem.symbol;
            }
            if (totalsComponent) {
                totalsComponent.updateData();
            }
            contentsElement.style.display = '';
        } else {
            contentsElement.style.display = 'none';
        }

        if (window.shoppingBasketLogics.displayTotals) {
            if (footerTotalsContainerElement) {
                footerTotalsContainerElement.style.display = '';
            }

            if (totalsContainerElement) {
                totalsContainerElement.style.display = '';
            }
        } else {
            if (footerTotalsContainerElement) {
                footerTotalsContainerElement.style.display = 'none';
            }

            if (totalsContainerElement) {
                totalsContainerElement.style.display = 'none';
            }
        }

        if (showInBasketDiscountsComponent) {
            showInBasketDiscountsComponent.innerHTML = '';
            var showInBasketDiscountsList = window.shoppingBasketLogics.getShowInBasketDiscountsList();
            for (var i = 0; i < showInBasketDiscountsList.length; i++) {
                var showInBasketDiscountElement = document.createElement('div');
                showInBasketDiscountElement.className = 'shoppingbasket_discount';
                showInBasketDiscountsComponent.appendChild(showInBasketDiscountElement);

                if (showInBasketDiscountsList[i].displayText) {
                    var discountTextElement = document.createElement('div');
                    discountTextElement.className = 'shoppingbasket_discount_text';
                    discountTextElement.innerHTML = showInBasketDiscountsList[i].basketText;
                    showInBasketDiscountElement.appendChild(discountTextElement);
                }

                if (showInBasketDiscountsList[i].displayProductsInBasket) {
                    var discountProductsElement = document.createElement('div');
                    discountProductsElement.className = 'shoppingbasket_discount_products';
                    showInBasketDiscountElement.appendChild(discountProductsElement);
                    for (var j = 0; j < showInBasketDiscountsList[i].products.length; j++) {
                        var currentProduct = showInBasketDiscountsList[i].products[j];
                        var discountProductElement = document.createElement('section');
                        discountProductElement.className = 'subcontentmodule_component subcontentmodule_square product_buttonsmall product_short productid_' + currentProduct.id;
                        discountProductsElement.appendChild(discountProductElement);

                        var titleElement = document.createElement('div');
                        titleElement.className = 'subcontentmodule_title product_buttonsmall_title';
                        titleElement.innerHTML = currentProduct.title;
                        discountProductElement.appendChild(titleElement);

                        var productContentElement = document.createElement('div');
                        productContentElement.className = 'subcontentmodule_content';
                        discountProductElement.appendChild(productContentElement);

                        var productHrefElement = document.createElement('a');
                        productHrefElement.className = 'product_buttonsmall_link';
                        productHrefElement.href = currentProduct.URL;
                        productContentElement.appendChild(productHrefElement);

                        if (currentProduct.originalName != '') {
                            var productThumbnailElement = document.createElement('div');
                            productThumbnailElement.className = 'product_buttonsmall_image_container';
                            productHrefElement.appendChild(productThumbnailElement);

                            var productImageElement = document.createElement('img');
                            productImageElement.className = 'product_buttonsmall_image';
                            productImageElement.src = window.baseURL + 'image/type:productSmallThumb/id:' + currentProduct.image + '/filename:' + currentProduct.originalName;
                            productImageElement.alt = currentProduct.title;
                            productThumbnailElement.appendChild(productImageElement);

                            if (currentProduct.icons || currentProduct.connectedDiscounts) {
                                var productIconsElement = document.createElement('div');
                                productIconsElement.className = 'product_buttonsmall_icons';
                                productThumbnailElement.appendChild(productIconsElement);

                                if (currentProduct.oldPrice) {
                                    var productDiscountCountainerElement = document.createElement('div');
                                    productDiscountCountainerElement.className = 'product_discount_container';
                                    productIconsElement.appendChild(productDiscountCountainerElement);

                                    var productOldPriceElement = document.createElement('span');
                                    productOldPriceElement.className = 'product_discount';
                                    productOldPriceElement.innerHTML = '-' + currentProduct.discountPercent + '%';
                                    productDiscountCountainerElement.appendChild(productOldPriceElement);
                                }

                                for (var q = 0; q < currentProduct.icons.length; q++) {
                                    var productIconElement = document.createElement('img');
                                    productIconElement.className = 'product_buttonsmall_icons_image';
                                    productIconElement.src = window.baseURL + 'image/type:productIcon/id:' + currentProduct.icons[q].image + '/filename:' + currentProduct.icons[q].originalName;
                                    productIconElement.alt = currentProduct.icons[q].title;
                                    productIconsElement.appendChild(productIconElement);
                                }

                                for (var q = 0; q < currentProduct.connectedDiscounts.length; q++) {
                                    if (currentProduct.connectedDiscounts[q].icon) {
                                        var productDiscountElement = document.createElement('img');
                                        productDiscountElement.className = 'product_buttonsmall_icons_image discount_icon';
                                        productDiscountElement.src = window.baseURL + 'image/type:productIcon/id:' + currentProduct.connectedDiscounts[q].icon + '/filename:' +
                                            currentProduct.connectedDiscounts[q].originalName;
                                        productDiscountElement.alt = currentProduct.connectedDiscounts[q].title;
                                        productIconsElement.appendChild(productDiscountElement);
                                    }
                                }
                            }
                        }

                        if (currentProduct.price) {
                            var productPriceElement = document.createElement('span');
                            productPriceElement.className = 'product_buttonsmall_price';
                            productPriceElement.innerHTML = currentProduct.price + window.selectedCurrencyItem.symbol;
                            productHrefElement.appendChild(productPriceElement);
                        }

                        if (currentProduct.isPurchasable) {
                            var productAddWrapElement = document.createElement('a');
                            productAddWrapElement.className = 'product_short_basket product_short_button product_buttonsmall_button button';
                            productAddWrapElement.href = currentProduct.URL;
                            productContentElement.appendChild(productAddWrapElement);

                            var productAddElement = document.createElement('span');
                            productAddElement.className = 'button_text';
                            productAddElement.innerHTML = currentProduct.addtobasket;
                            productAddWrapElement.appendChild(productAddElement);
                        }

                        new ProductShortComponent(discountProductElement);
                    }
                }
            }
        }
    };
    var submitForm = function(event) {
        eventsManager.preventDefaultAction(event);
        if (conditionsCheckboxInput) {
            if (conditionsCheckboxInput.checked) {
                formElement.submit();
            } else {
                var message = [];
                var additionalContainerClassName = 'notice_box';
                message['title'] = translationsLogics.get('shoppingbasket.conditions');
                message['content'] = '<a target="ART" class="modal_link" href="' + window.conditionsLink + '">' + translationsLogics.get('shoppingbasket.conditions_error') + '</a>';
                message['footer'] = translationsLogics.get('shoppingbasket.agreewithconditions');

                new ModalActionComponent(conditionsCheckboxInput, false, submitButtonElement, additionalContainerClassName, '', message); // checkbox-input, footer advanced, element for position, messages
            }
        } else {
            formElement.submit();
        }
    };
    init();
};
