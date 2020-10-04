window.ProductDetailsComponent = function(componentElement) {
    var productData;
    var productId;
    var basketButton;
    var amountMinusElement;
    var amountPlusElement;
    var amountInput;
    var minimumOrder;
    var priceElements = [];
    var oldPriceElements = [];
    let inquiryLink;
    let inquiryForm;
    var optionsSelected = true;
    var selectedOptions = [];
    var selectionIndex = [];
    var selectedOptionsText = [];
    var selections = [];
    var lastChangedSelection = null;
    var gallery = null;
    var self = this;
    var productPrice;
    var options;
    let init = function() {
        if (window.productDetailsData) {
            productData = window.productDetailsData;
        } else {
            return false;
        }

        productId = parseInt(componentElement.className.split('productid_')[1], 10);

        minimumOrder = 1;
        var minimumOrderElement = _('.product_minimumorder_value', componentElement)[0];
        if (minimumOrderElement) {
            minimumOrder = parseInt(minimumOrderElement.innerHTML, 10);
        }
        if (basketButton = _('.product_details_button', componentElement)) {
            for (let i = 0; i < basketButton.length; i++) {
                new BasketButtonComponent(basketButton[i], basketButtonClickHandler);
            }
        }
        if (amountMinusElement = _('.product_details_amount_minus', componentElement)) {
            for (let i = 0; i < amountMinusElement.length; i++) {
                eventsManager.addHandler(amountMinusElement[i], 'click', minusClickHandler);
            }
        }
        if (amountPlusElement = _('.product_details_amount_plus', componentElement)) {
            for (let i = 0; i < amountPlusElement.length; i++) {
                eventsManager.addHandler(amountPlusElement[i], 'click', plusClickHandler);
            }
        }
        if (amountInput = _('.product_details_amount_input', componentElement)) {
            for (let i = 0; i < amountInput.length; i++) {
                eventsManager.addHandler(amountInput[i], 'change', amountChangeHandler);
                inputChangeHandler(amountInput[i]);
            }
        }
        if (inquiryLink = _('.product_details_inquiry_link', componentElement)[0]) {
            eventsManager.addHandler(inquiryLink, 'click', inquiryLinkHandler);
            domHelper.addClass(inquiryLink, 'toggleable_component_trigger');
        }
        if (inquiryForm = _('.product_details_inquiry_form', componentElement)[0]) {

            domHelper.addClass(inquiryForm, 'toggleable_component_content');
            var feedback_block = _('.feedback_block', inquiryForm)[0];
            var defaultBehaviour = 'hidden';
            if (domHelper.hasClass(feedback_block, 'feedback_submitted')) {
                defaultBehaviour = 'shown';
            } else {
                domHelper.addClass(inquiryForm, 'toggleable_component_content_hidden');
            }

            options = {
                contentElement: inquiryForm,
                markerElement: inquiryLink,
                afterOpenCallback: inquiryFormOpened,
                defaultBehaviour: defaultBehaviour,
            };
            new ToggleableContainer(componentElement, options);
        }
        priceElements = componentElement.querySelectorAll('.product_details_price_digits');
        oldPriceElements = componentElement.querySelectorAll('.product_details_oldprice_digits');
        var selectionElements = _('.product_details_option_control', componentElement);
        for (i = selectionElements.length; i--;) {
            var selection = new ProductDetailsSelectionComponent(self, selectionElements[i]);
            if (selection.hasSelector()) {
                selections.push(selection);
                selectionIndex[selection.getId()] = selection;
            }
        }
        if (window.productParametersHintsInfo) {
            var parameterElements = _('.product_details_parameter', componentElement);
            for (i = parameterElements.length; i--;) {
                new ProductDetailsParameterComponent(parameterElements[i]);
            }
        }
        var product = getProduct();
        tracking.detailTracking(product);
        gallery = galleriesLogics.getGalleryInfo(productId);
        refresh();
    };
    let inquiryFormOpened = function() {
        TweenLite.to(window, 1, {scrollTo: {y: inquiryForm.offsetTop}, ease: Power2.easeOut});
    };
    let inquiryLinkHandler = function(event) {
        if (_('.product_details_inquiry_form', componentElement)[0]) {
            event.preventDefault();
        }
    };

    let inputChangeHandler = function(element) {
        eventsManager.addHandler(element, 'input', function() {
            changeInput(element.value);
        });
    };
    var basketButtonClickHandler = function() {
        if (!optionsSelected) {
            // alert(window.translationsLogics.get('product.details_must_select_options'));
            controller.fireEvent('shoppingBasketProductAddFailure', 'product.details_must_select_options');
            return;
        }
        var optionsArgument = selectedOptions;
        var amount = amountInput ? amountInput[0].value : minimumOrder;
        if (amount % minimumOrder != 0) {
            amount = minimumOrder;
        }
        shoppingBasketLogics.addProduct(productId, amount, optionsArgument);
    };

    var plusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput[0].value, 10);
        amount = amount + minimumOrder;
        changeInput(amount);
    };
    var changeInput = function(value) {
        if (value) {
            for (let i = 0; i < amountInput.length; i++) {
                amountInput[i].value = value;
            }
        }
    };
    var minusClickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        var amount = parseInt(amountInput[0].value, 10);
        amount = amount - minimumOrder;

        if (amount < 1) {
            amount = minimumOrder;
        }
        changeInput(amount);
    };
    var amountChangeHandler = function(event) {
        event.preventDefault();
        var amount = parseInt(amountInput[0].value, 10);
        if (isNaN(amount) || (amount % minimumOrder != 0)) {
            amount = minimumOrder;
        }
        if (amountInput[0].value != amount) {
            changeInput(amount);
        }
    };
    var refresh = function() {
        let i;
        var j;
        optionsSelected = true;
        selectedOptions = [];
        selectedOptionsText = [];
        if (!productData) {
            return;
        }
        let influentialOptions = [];
        for (i = selections.length; i--;) {
            var value = selections[i].getValue();
            var text = selections[i].getPlaceholder();
            if (!value) {
                optionsSelected = false;
            } else {
                if (selections[i].isInfluential()) {
                    influentialOptions.push(value);
                }
                selectedOptions.push(value);
                selectedOptionsText.push(text);
            }
        }
        var oldPrice = 0;
        var price = parseFloat(productData.price.replace(' ', ''));
        if (productData.oldPrice) {
            oldPrice = parseFloat(productData.oldPrice.replace(' ', ''));
        }

        if (influentialOptions.length > 0) {
            influentialOptions.sort(function(a, b) {
                return a - b;
            });
            var comboCode = influentialOptions.join(';') + ';';

            if (productData.selectionsPricings[comboCode]) {
                price = parseFloat(productData.selectionsPricings[comboCode].replace(' ', ''));
            }
            if (productData.selectionsOldPricings[comboCode]) {
                oldPrice = parseFloat(productData.selectionsOldPricings[comboCode].replace(' ', ''));
            }
        }
        if (typeof productData.basketSelectionsInfo !== 'undefined') {
            for (i = 0; i < productData.basketSelectionsInfo.length; i++) {
                for (j = 0; j < productData.basketSelectionsInfo[i]['productOptions'].length; j++) {
                    var option = productData.basketSelectionsInfo[i]['productOptions'][j];
                    if (selectedOptions.indexOf(option.id) >= 0) {
                        if (option.price) {
                            price += option.price;
                            oldPrice += option.price;
                        }
                    }
                }
            }
        }

        if (priceElements) {
            for (i = priceElements.length; i--;) {
                priceElements[i].innerHTML = price;
            }
        }

        if (oldPriceElements) {
            for (i = oldPriceElements.length; i--;) {
                oldPriceElements[i].innerHTML = oldPrice;
            }
        }
    };

    var updateGallery = function() {
        if (gallery && lastChangedSelection) {
            var value = lastChangedSelection.getValue();
            var optionImage = productData.selectionsImages[value];
            if (optionImage) {
                gallery.stopSlideShow();
                gallery.displayImage(optionImage);
            }
        }
    };

    this.selectionChanged = function(selection) {
        lastChangedSelection = selection;
        refresh();
        updateGallery();
    };

    var getProduct = function() {
        var quantity = null;
        if (amountInput) {
            quantity = amountInput.value;
        }
        var price = null;
        if (priceElements && priceElements[0]) {
            if (priceElements[0]) {
                price = priceElements[0].innerText;
            }
        }
        return {
            'id': productId,
            'name': productData.title_ga,
            'category': productData.category_ga,
            'variant': selectedOptionsText,
            'price': price,
            'quantity': quantity,
        };
    };

    this.getProductPrice = function() {
        return productPrice;
    };

    this.getSelection = function() {
        return selections;
    };

    this.setNewPrice = function(price) {
        for (let i = 0; i < priceElements.length; i++) {
            priceElements[i].innerHTML = price;
        }
    };

    this.getSelectionValue = function(id) {
        return selectionIndex[id].getValue();
    };
    init();
};

window.ProductDetailsSelectionComponent = function(detailsComponent, componentElement) {
    let id = '';
    let influential = '';
    var selectElement;
    var radioElements;
    var self = this;

    let init = function() {
        id = componentElement.getAttribute('data-elementid');
        influential = !!parseInt(componentElement.getAttribute('data-influential'));
        selectElement = _('select.product_details_option_selector', componentElement)[0];
        if (selectElement) {
            eventsManager.addHandler(selectElement, 'change', change);
        } else {
            radioElements = _('.product_details_option_radio_item_control', componentElement);
            eventsManager.addHandler(componentElement, 'click', change);
        }
        if (window.productParametersHintsInfo && window.productParametersHintsInfo[id]) {
            var hintElement = _('.product_details_option_hint', componentElement.parentElement)[0];
            if (hintElement) {
                var hints = window.productParametersHintsInfo[id];
                var hintContent = hints.join('<hr/>');
                new ToolTipComponent({
                    'referralElement': hintElement,
                    'popupText': hintContent,
                });
                hintElement.classList.add('product_details_option_hint_show');
            }
        }
    };
    var change = function(event) {
        detailsComponent.selectionChanged(self);
    };
    this.isInfluential = function() {
        return influential;
    };
    this.getId = function() {
        return id;
    };
    this.getValue = function() {
        if (selectElement) {
            return parseInt(selectElement.value, 10);
        } else if (radioElements) {
            for (let i = radioElements.length; i--;) {
                if (radioElements[i].checked) {
                    return parseInt(radioElements[i].value, 10);
                }
            }
        }
        return false;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.getPlaceholder = function() {
        if (selectElement) {
            return selectElement.value;
        } else if (radioElements) {
            for (let i = radioElements.length; i--;) {
                if (radioElements[i].checked) {
                    return radioElements[i].placeholder;
                }
            }
        }
        return false;
    };
    this.hasSelector = function() {
        return selectElement || radioElements.length;
    };

    init();
};

window.ProductDetailsParameterComponent = function(componentElement) {
    var hintElement;
    var hints = [];

    let init = function() {
        let id = componentElement.className.slice(componentElement.className.indexOf('product_details_parameter_id_') + 29);
        if (id.indexOf(' ') > 0) {
            id = id.slice(0, id.indexOf(' '));
        }
        hintElement = _('.product_details_parameter_hinttrigger', componentElement)[0];
        if (window.productParametersHintsInfo[id] && hintElement) {
            hints = window.productParametersHintsInfo[id];
            var hintContent = hints.join('<hr/>');
            new ToolTipComponent(hintElement, hintContent, false, 'product_details_parameter_tooltip');
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};