window.AjaxProductListComponent = function(productElement, productId, productsListSetsArray) {
    var self = this;
/*
    var ajaxSearchResultsComponent = false;
    var inputCheckDelay = 400;
    var keyUpTimeOut;
    var searchStringLimit = 2;
    var resultsLimit = 30;

    var customResultsElement;
    var customShowedElementComponents;
    var totalsElement;
    var getValueCallback;
    var clickCallback;
    var resultsUpdateCallback;
    var types;
    var searchString;
    var apiMode = 'public';
    var filters = '';
    var position = 'absolute';
    this.displayInElement = false;
    this.displayTotals = false;

    this.productElement = null;
    this.inputElement = null;
*/

    var productElementQuickView;
    var productElementQuickViewUrl;
    var productElementQuickViewLink;

    var init = function() {
        self.productElement = productElement;

        if(productsListSetsArray.length > 0) {
            [].forEach.call(productsListSetsArray, function(productsListSet, i) {
                if (productsListSet === 'quickview') {
                    self.productElement.classList += ' product_with_quickview';
                    productElementQuickView = document.createElement('div');
                    productElementQuickView.className = 'product_quickview_trigger';
                    productElementQuickViewLink = document.createElement('a');
                    productElementQuickViewLink.className = 'product_quickview_link product_quickview_button';
                    productElementQuickViewUrl = '/ajaxProductList/listElementId:' + window.currentElementId + '/elementId:' + productId + '/';
                    productElementQuickViewLink.href = productElementQuickViewUrl;
                    productElementQuickViewLink.innerText = translationsLogics.get('product.quickview');

                    productElementQuickView.appendChild(productElementQuickViewLink);
                    self.productElement.appendChild(productElementQuickView);

                    productElementQuickView.addEventListener("click", function(e){
                        clickHandler(e,productId,productElementQuickView,productElementQuickViewUrl);
                    }, false);
                }
            });
        }
        //quickview
/*
        var request = new JsonRequest(url,
            function(responseStatus, requestName, responseData) {
                return receiveData(responseStatus, requestName, responseData,
                    callBack);
            }, 'ajaxSearch');
        request.send();
 */
        //    console.log(productsListSetsArray)
/*
        self.inputElement = productElement;
        self.inputElement.autocomplete = 'off';
        if (typeof parameters !== 'undefined') {
            parseParameters(parameters);
        }
        ajaxSearchResultsComponent = new AjaxSearchResultsComponent(self,
            customResultsElement);
        self.inputElement.addEventListener('keydown', keyPressHandler);
*/

        // if (self.inputElement.parentElement.className === 'ajaxselect_container' ||
        //     self.inputElement.parentElement.className ===
        //     'ajaxitemsearch_container') {
        //     self.inputElement.addEventListener('focus', function() {
        //         var container = self.inputElement.parentElement;
        //         container.style.border = '1px solid #6bbbff';
        //     });
        //     self.inputElement.addEventListener('focusout', function() {
        //         var container = self.inputElement.parentElement;
        //         container.style.border = '1px solid #e2e2e5';
        //     });
        // }

        // if (!customResultsElement) {
        //     window.addEventListener('click', windowClickHandler);
        // }
        // controller.addListener('ajaxSearchResultsReceived', updateData);
    };
/*
    var parseParameters = function(parameters) {
        if (typeof parameters.clickCallback !== 'undefined') {
            clickCallback = parameters.clickCallback;
        }
        if (typeof parameters.resultsUpdateCallback !== 'undefined') {
            resultsUpdateCallback = parameters.resultsUpdateCallback;
        }
        if (typeof parameters.getValueCallback !== 'undefined') {
            getValueCallback = parameters.getValueCallback;
        }
        if (typeof parameters.types !== 'undefined') {
            types = parameters.types;
        }
        if (typeof parameters.apiMode !== 'undefined') {
            apiMode = parameters.apiMode;
        }
        if (typeof parameters.searchStringLimit !== 'undefined') {
            searchStringLimit = parseInt(parameters.searchStringLimit, 10);
        }
        if (typeof parameters.resultsLimit !== 'undefined') {
            resultsLimit = parseInt(parameters.resultsLimit, 10);
        }
        if (typeof parameters.filters !== 'undefined') {
            filters = parameters.filters;
        }
        if (typeof parameters.displayInElement !== 'undefined') {
            self.displayInElement = parameters.displayInElement;
        }
        if (typeof parameters.displayTotals !== 'undefined') {
            self.displayTotals = parameters.displayTotals;
        }
        if (typeof parameters.position !== 'undefined') {
            position = parameters.position;
        }
        if (typeof parameters.totalsElement !== 'undefined') {
            totalsElement = parameters.totalsElement;
        }
        if (typeof parameters.customResultsElement != 'undefined') {
            customResultsElement = parameters.customResultsElement;
        }
        if (typeof parameters.showedElementComponents != 'undefined') {
            customShowedElementComponents = parameters.showedElementComponents;
        }
    };


*/
    var clickHandler = function(e,productId,productElementQuickView,productElementQuickViewUrl) {
        e.preventDefault();
        e.stopPropagation();

        self.sendQuery(getProductData,productElementQuickViewUrl);

    //    window.jsonData.product = 'product': $element->getElementData()|json_encode};


        /*

                var productQuickView = new AjaxSearchResultsItemComponent(elementsList[i],
                    parentObject);
                productElementQuickView.appendChild(item.productElement);

        */
};
    var getProductData = function(responseData) {
        new AjaxProductListSingleItem(productId,responseData.product[0]);


        // if (allElements.length > 0) {
        //     ajaxSearchResultsComponent.updateData(allElements);
        //     ajaxSearchResultsComponent.displayComponent();
        // } else {
        //     ajaxSearchResultsComponent.hideComponent();
        // }
        // if (resultsUpdateCallback) {
        //     resultsUpdateCallback(allElements);
        // }
    };


    var receiveData = function(responseStatus, requestName, responseData, callBack) {
        if (responseStatus === 'success' && responseData) {
           callBack(responseData);
        }
        else {
            controller.fireEvent('ajaxSearchResultsFailure', responseData);
        }
    };
    this.sendQuery = function(callBack, reqUrl) {
        //JsonRequest > requestURL, callback, requestName, requestParameters, formData
        var request = new JsonRequest(reqUrl,
            function(responseStatus, requestName, responseData) {
                return receiveData(responseStatus, requestName, responseData, callBack);
            }, 'ajaxProductList');

        request.send();
    };
    init();
};

window.AjaxProductListSingleItem = function(productId,productItem) {
    // var productElement;
    var quickElement;
    var quickElementInner = [];
    var productAttrs = [
        'title',
        'imageUrl',
        'introduction',
        'availability',
        'price',
        'oldPrice',
    ];


    var addToBasket = document.createElement('a');
    addToBasket.className = 'quickview_addto_basket product_short_basket button';
    addToBasket.setAttribute('href', productItem.url);
    addToBasket.textContent = translationsLogics.get('product.addtobasket');
    var additionalContainerClassName = 'notice_box product_quickview_box';
    var message = [];
    var quickElementAttr = [];

    // var onBasketButtonClick = function(event) {
    //     var amount = amountInputElement ? amountInputElement.value : minimumOrder;
    //     if (amount % minimumOrder != 0) {
    //         amount = minimumOrder;
    //     }
    //     var variation = '';
    //     if (optionSelectElements) {
    //         for (var i = 0; i < optionSelectElements.length; ++i) {
    //             if (i != 0) {
    //                 variation += ', ';
    //             }
    //             variation += optionSelectElements[i].value;
    //         }
    //     }
    //     shoppingBasketLogics.addProduct(productId, amount, variation);
    // };
    //
    // if (basketButton = componentElement.querySelector('.product_short_basket')) {
    //     new BasketButtonComponent(basketButton, onBasketButtonClick, productId);
    // }


    // var displayComponent = function() {
    //     componentElement.style.display = 'block';
    //     centerComponent.updateContents();
    //     window.addEventListener('keydown', keyDownHandler);
    // };
    // var closeClick = function(e) {
    //     eventsManager.preventDefaultAction(e);
    //     self.setDisplayed(false);
    // };
   // DarkLayerComponent.showLayer();
  //  productElement = document.querySelector('.productid_' + productId);
    quickElement = document.createElement('div');
    quickElement.className = 'product_quickview';

    productAttrs.forEach(function(productAttr, i) {
        quickElementInner[i] = document.createElement('div');
     //   console.log(typeof(productItem[productAttr]))
        if (typeof(productItem[productAttr]) === 'object') {
            for (var key in productItem[productAttr]) {
                quickElementInner[i].className = 'product_quickview_' + productAttr.toLowerCase() + ' ' + key.toLowerCase();
                quickElementAttr['val'] = productItem[productAttr][key];
            }
        }
        else {
            quickElementInner[i].className = 'product_quickview_' + productAttr;
            quickElementAttr['val'] = productItem[productAttr];
        }

        if (productAttr === 'imageUrl') {
            quickElementInner[i].innerHTML = '<img src="' + quickElementAttr['val'] + '">';
        }
        else {
            quickElementInner[i].innerHTML = quickElementAttr['val'];
        }
        quickElement.appendChild(quickElementInner[i]);
    });
    // quickElementInner[1] = document.createElement('div');
    // quickElementInner[1].className = 'product_quickview_title';
    // quickElementInner[1].innerHTML = productItem.title;


 //   smartyRenderer.setTemplate('product.detailed.js.tpl');

    // var quickTpl = new jSmart( document.getElementById('product.detailed.js.tpl').innerHTML );
    // var quickRes = quickTpl.fetch( {} );
 //   quickElement.innerHTML = quickRes;

    var buildHtml = function() {
        var templateInternal = 'product.detailed.js.tpl';
        var compiled = new jSmart(window.templates[templateInternal]);

        quickElement.innerHTML = compiled.fetch({
            // 'element': self,
            // 'selectedCurrencyItem': window.selectedCurrencyItem,
            // 'checkout': checkout,
        });
        /*

                if (rowsContainerElement = componentElement.querySelector('.shoppingbasket_table_rows')) {
                    var productRows = rowsContainerElement.querySelectorAll('.shoppingbasket_table_product');
                    if (productRows) {
                        for (var i = 0; i < productRows.length; i++) {
                            var basketProductId = productRows[i].dataset.id;
                            if (typeof productComponentsIndex[basketProductId] !== 'undefined') {
                                productComponentsIndex[basketProductId].setComponentElement(productRows[i]);
                            }
                        }
                    }
                }
        */

    };

    buildHtml();
    message['title'] = productItem.title;
    message['content'] = quickElement.outerHTML;
    message['footer'] = addToBasket.outerHTML;

    // window.ModalActionComponent = function(checkboxElement, footerElement, elementForPosition, additionalClassName, bubbleCloseTag, message) {



    var modalActionComponent = new ModalActionComponent(false, 'multiple', quickElement, additionalContainerClassName, false, message); // checkbox-input, multiple footer buttons, element for position, messages
    if(modalActionComponent) {
/*
        var onBasketButtonClick = function(event) {
            var amount = amountInputElement ? amountInputElement.value : minimumOrder;
            if (amount % minimumOrder != 0) {
                amount = minimumOrder;
            }
            var variation = '';
            if (optionSelectElements) {
                for (var i = 0; i < optionSelectElements.length; ++i) {
                    if (i != 0) {
                        variation += ', ';
                    }
                    variation += optionSelectElements[i].value;
                }
            }
            console.log(productId, amount)
            shoppingBasketLogics.addProduct(productId, amount, variation);
        };
       // console.log(quickElement.querySelector('.product_short_basket'))

        if (addToBasket) {
            new BasketButtonComponent(addToBasket, onBasketButtonClick, productId);
               console.log(productId)
        }
*/
    }
    // new ModalActionComponent(false, 'multiple', quickElement, additionalContainerClassName, false, message); // checkbox-input, multiple footer buttons, element for position, messages
    // new ProductShortComponent(addToBasket);


  //  document.body.appendChild(quickElement);

    // console.log(productId)
};

/*
window.AjaxSearchResultsComponent = function(parentObject, customResultsElement) {
    var productElement;
    var contentElement;
    var resultItems = [];
    var selectedIndex = false;
    var self = this;
    var position;
    this.displayed = false;


    var init = function() {
        position = parentObject.getPosition();
        if (customResultsElement) {
            productElement = customResultsElement;
        } else {
            productElement = self.makeElement('div', 'ajaxsearch_results_block');
        }
        productElement.addEventListener('click', clickHandler);

        contentElement = document.createElement('div');
        contentElement.className = 'ajaxsearch_results_list';
        productElement.appendChild(contentElement);
        if (parentObject.displayInElement) {
            parentObject.displayInElement.appendChild(productElement);
        } else {
            if (!customResultsElement) {
                document.body.appendChild(productElement);
            }
        }

        eventsManager.addHandler(productElement, 'click', clickHandler);
        eventsManager.addHandler(window, 'resize', updateSizes);
    };
    this.reset = function() {
        while (contentElement.firstChild) {
            contentElement.removeChild((contentElement.firstChild));
        }
    };
    this.updateData = function(elementsList) {
        self.reset();
        resultItems = [];

        for (var i = 0; i < elementsList.length; i++) {
            var item = new AjaxSearchResultsItemComponent(elementsList[i],
                parentObject);
            contentElement.appendChild(item.productElement);

            resultItems.push(item);
        }
    }

*/