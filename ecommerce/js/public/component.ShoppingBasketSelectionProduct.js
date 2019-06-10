window.ShoppingBasketSelectionProduct = function(initData) {
    var self = this;

    var productData = false;
    var changeTimeOut = false;
    var keyUpDelay = 400;
    var amountUpDelay = 200;
    var minimumOrder = 1;

    var imageElement;
    var titleElement;
    var codeElement;
    var descriptionElement;
    var priceCellContainer;
    var priceElement;
    var fullPriceElement;
    var totalPriceElement;
    var amountPlusButton;
    var amountMinusButton;
    var amountInput;
    var removeButton;
    var categoryTitle;

    this.componentElement = false;
    this.basketProductId = false;

    var init = function() {
        productData = initData;
        self.basketProductId = productData.basketProductId;
        minimumOrder = productData.minimumOrder;
        createDomStructure();
        self.updateContents();
    };
    var createDomStructure = function() {
        self.componentElement = self.makeElement('tr', 'shoppingbasket_table_item');
        var cellElement;

        /**
         imageCell:
         0: "imageElement"
         1: "codeElement"
         */
        /* default
             imageCell:
             0: "imageElement"
             */
        if (basketView['imageCell']) {
            var imageCell = basketView['imageCell'];
            cellElement = self.makeElement('td', 'shoppingbasket_table_image_container', self.componentElement);

            if (imageCell.indexOf('imageElement') > -1) {
                imageElement = cellElement.appendChild(self.makeElement('img', 'shoppingbasket_table_image', cellElement));
            }
            if (imageCell.indexOf('codeElement') > -1) {
                var codeElement = self.makeElement('div', 'shoppingbasket_code_container', self.cellElement);
                codeElement.innerHTML = window.translationsLogics.get('shoppingbasket.productstable_productcode') + ': ' + productData.code;
                cellElement.appendChild(codeElement);
            }
        }

        /*
         infoCell:
         0: "productContainerElement"
         1: "productInnerElement"
         2: "categoryTitleElement"
         3: "titleElement"
         4: "descriptionElement"
         */
        /* default
             infoCell:
             0: "tableTitleElement"
             3: "titleElement"
             3: "codeElement"
             4: "descriptionElement"
             */

        if (basketView['infoCell']) {
            var infoCell = basketView['infoCell'];
            if (infoCell.indexOf('tableTitleElement') > -1) {
                cellElement = self.makeElement('td', 'shoppingbasket_table_title', self.componentElement);
            } else if (infoCell.indexOf('productContainerElement') > -1) {
                cellElement = self.makeElement('td', 'shoppingbasket_table_product_container', self.componentElement);
            }
            if (infoCell.indexOf('productInnerElement') > -1) {
                cellElement = self.makeElement('div', 'shoppingbasket_table_product_inner', cellElement);
            }
            if (infoCell.indexOf('categoryTitleElement') > -1) {
                categoryTitle = self.makeElement('div', 'shoppingbasket_table_category_title', cellElement);
                domHelper.addClass(categoryTitle, 'text_uppercase');
            }
            if (infoCell.indexOf('titleElement') > -1) {
                titleElement = self.makeElement('a', 'shoppingbasket_table_title', cellElement);
            }
            if (infoCell.indexOf('codeElement') > -1) {
                codeElement = self.makeElement('div', 'shoppingbasket_table_code', cellElement);
            }
            if (infoCell.indexOf('descriptionElement') > -1) {
                descriptionElement = self.makeElement('div', 'shoppingbasket_table_description', cellElement);
            }
        }

        if (basketView['priceCell']) {
            var priceCell = basketView['priceCell'];
            cellElement = self.makeElement('td', 'shoppingbasket_table_price', self.componentElement);
            priceCellContainer = cellElement;
            var priceElementClassName = 'shoppingbasket_table_price_value';
            if (priceCell.indexOf('fullPriceElement') > -1) {
                fullPriceElement = self.makeElement('div', 'shoppingbasket_table_full_price_value', cellElement);
                if (productData.salesPrice != productData.price) {
                    domHelper.addClass(fullPriceElement, 'lined_price');
                    fullPriceElement.style.display = 'block';
                    priceElementClassName += ' new_price';
                }
            }
            if (priceCell.indexOf('priceTitleElement') > -1) {
                var priceTitleElement = self.makeElement('div', 'shoppingbasket_table_price_title', cellElement);
                priceTitleElement.innerHTML = translationsLogics.get('shoppingbasket.table_price_title');
            }
            priceElement = self.makeElement('div', priceElementClassName, cellElement);
        }

        /*
         amountCell:
         0: "amountChangeElementWrap"
         1: "amountChangeElement"
         2: "amountTitleElement"
         */
        /* default
             amountCell:
             []
             */
        if (basketView['amountCell']) {
            var amountCell = basketView['amountCell'];
            cellElement = self.makeElement('td', 'shoppingbasket_table_amount', self.componentElement);
            var amountContainerElement;
            if (amountCell.indexOf('amountChangeElementWrap') > -1) {
                var amountChangeWrapper = self.makeElement('div', 'shoppingbasket_table_amount_wrapper', cellElement);
                var amountTitle = self.makeElement('div', 'shoppingbasket_table_amount_title', amountChangeWrapper);
                amountTitle.innerHTML = translationsLogics.get('shoppingbasket.table_amount_title');
                amountContainerElement = self.makeElement('div', 'shoppingbasket_table_amount_container', amountChangeWrapper);
            } else {
                amountContainerElement = self.makeElement('div', 'shoppingbasket_table_amount_container', cellElement);
            }

            amountMinusButton = self.makeElement('a', 'button shoppingbasket_table_amount_minus', amountContainerElement);
            amountMinusButton.innerHTML = '<span class="button_text">-</span>';
            eventsManager.addHandler(amountMinusButton, 'click', minusClickHandler);

            amountInput = self.makeElement('input', 'input_component shoppingbasket_table_amount_input', amountContainerElement);
            eventsManager.addHandler(amountInput, 'keyup', amountKeyUpHandler);
            eventsManager.addHandler(amountInput, 'change', amountChangeHandler);
            new window.InputComponent({'componentClass': 'shoppingbasket_table_amount_block', 'inputElement': amountInput});

            amountPlusButton = self.makeElement('a', 'button shoppingbasket_table_amount_plus', amountContainerElement);
            amountPlusButton.innerHTML = '<span class="button_text">+</span>';
            eventsManager.addHandler(amountPlusButton, 'click', plusClickHandler);
        }
        /*
         totalPriceCell:
         0: "totalPriceElement"
        */
        /* default
             priceCell:
             0: "totalPriceElement"
            */

        if (basketView['totalPriceCell']) {
            // total cell
            cellElement = self.makeElement('td', 'shoppingbasket_table_totalprice', self.componentElement);
            totalPriceElement = self.makeElement('span', 'shoppingbasket_table_totalprice_value', cellElement);
        }

        /*
         removeCell:
         0: "deleteContainerParent"
         0: "deleteContainer"
         1: "deleteElementText"
         */
        /* default
             removeCell:
             1: "deleteElementButton"
             */
        if (basketView['removeCell']) {
            var removeCell = basketView['removeCell'];
            var deleteContainer;
            var deleteElement;
            if (removeCell.indexOf('deleteContainer') > -1) {
                deleteContainer = self.makeElement('div', 'shoppingbasket_table_delete_container', priceCellContainer);
                deleteElement = self.makeElement('div', 'shoppingbasket_table_remove', deleteContainer);
            } else {
                cellElement = self.makeElement('td', 'shoppingbasket_table_remove', self.componentElement);
            }

            if (removeCell.indexOf('deleteElementText') > -1) {
                deleteElement.innerHTML = translationsLogics.get('shoppingbasket.productstable_remove');
                eventsManager.addHandler(deleteElement, 'click', removeClickHandler);
            } else {
                removeButton = self.makeElement('a', 'shoppingbasket_table_remove_button', cellElement);
                eventsManager.addHandler(removeButton, 'click', removeClickHandler);
            }
        }

    };

    var plusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput.value, 10);
        amount = amount + minimumOrder;
        amountInput.value = amount;

        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, amountUpDelay);
    };
    var minusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput.value, 10);
        amount = amount - minimumOrder;

        if (amount < minimumOrder) {
            amount = minimumOrder;
        }

        amountInput.value = amount;

        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, amountUpDelay);
    };
    var amountKeyUpHandler = function() {
        window.clearTimeout(changeTimeOut);
        changeTimeOut = window.setTimeout(changeAmount, keyUpDelay);
    };
    var changeAmount = function() {
        var amount = parseInt(amountInput.value, 10);
        if (amount % minimumOrder != 0) {
            amount = minimumOrder;
        }
        if (!isNaN(amount) && amount > 0) {
            registerEventHandlers();
            window.shoppingBasketLogics.changeAmount(self.basketProductId, amount);
        }
    };

    var shoppingBasketProductAdditionHandler = function() {
        unRegisterEventHandlers();
    };
    var shoppingBasketProductAddFailureHandler = function() {
        unRegisterEventHandlers();
        // alert(window.translationsLogics.get('product.quantityunavailable'));
        if (addToBasketButtonAction) {
            var message = [];
            var additionalContainerClassName = 'notice_box';
            var additionalClassName = 'notice_basket';
            message['title'] = window.productDetailsData.name || window.productDetailsData.name_ga;
            message['content'] = window.translationsLogics.get('product.quantityunavailable');
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

        amountInput.value--;
    };

    var registerEventHandlers = function() {
        controller.addListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.addListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };

    var unRegisterEventHandlers = function() {
        controller.removeListener('shoppingBasketProductAdded', shoppingBasketProductAdditionHandler);
        controller.removeListener('shoppingBasketProductAddFailure', shoppingBasketProductAddFailureHandler);
    };

    var amountChangeHandler = function() {
        window.clearTimeout(changeTimeOut);

        var amount = parseInt(amountInput.value, 10);
        if (isNaN(amount) || amount < 1) {
            amount = 1;
        }
        if (amountInput.value != amount) {
            amountInput.value = amount;
        }
        changeAmount();
    };

    var removeClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        window.shoppingBasketLogics.removeProduct(self.basketProductId);
    };

    this.updateContents = function() {
        if (productData.image != '') {
            imageElement.src = productData.image;
            imageElement.style.display = 'block';
        } else {
            imageElement.style.display = 'none';
        }
        if (categoryTitle) {
            categoryTitle.innerHTML = productData.category;
        }
        titleElement.innerHTML = productData.title;
        titleElement.href = productData.url;
        if (codeElement) {
            codeElement.innerHTML = window.translationsLogics.get('shoppingbasket.productstable_productcode') + ': ' + productData.code;
        }
        var variations = [];
        if (productData.variation) {
            if (typeof productData.variation == 'object' && productData.variation.length) {
                variations = productData.variation;
            } else if (typeof productData.variation == 'string') {
                variations.push(productData.variation);
            }
        }
        if (variations.length) {
            var variationHtml = [];
            variations.forEach(function(variation, i) {
                var variationsArray = variation.split(':');
                variationHtml[i] = '<span class="variation_name">' + variationsArray[0] + '</span><span class="variation_separator"></span><span class="variation_value">' + variationsArray[1] + '</span>';
            });
            variations = variationHtml;
            descriptionElement.innerHTML = '<p>' + variations.join('</p><p>') + '</p>';
        } else {
            descriptionElement.innerHTML = '';
        }
        if (!productData.emptyPrice) {
            priceElement.innerHTML = productData.price + ' ' + window.selectedCurrencyItem.symbol;

            if (productData.salesPrice != productData.price) {
                fullPriceElement.innerHTML = productData.price + ' ' + window.selectedCurrencyItem.symbol;
                fullPriceElement.style.display = 'block';
            } else {
                fullPriceElement.style.display = 'none';
            }
            if (totalPriceElement) {
                totalPriceElement.innerHTML = productData.totalSalesPrice + ' ' + window.selectedCurrencyItem.symbol;
            }
        }
        amountInput.value = productData.amount;
    };

    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionProduct.prototype);
