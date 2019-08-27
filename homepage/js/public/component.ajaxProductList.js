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
             //   new AjaxSelectComponent(genericIconFormElement, genericIconFormElement.dataset.select, 'admin');
                if (productsListSet === 'quickview') {
                    productElementQuickView = document.createElement('div');
                    productElementQuickView.className = 'product_quickview';
                    productElementQuickViewLink = document.createElement('a');
                    productElementQuickViewLink.className = 'product_quickview_link';
                    productElementQuickViewUrl = '/ajaxProductList/listElementId:' + window.currentElementId + '/elementId:' + productId;
                    productElementQuickViewLink.href = productElementQuickViewUrl;
                    productElementQuickViewLink.innerText = translationsLogics.get('product.quickview');

                    productElementQuickView.appendChild(productElementQuickViewLink);
                    self.productElement.appendChild(productElementQuickView);

                    productElementQuickView.addEventListener("click", function(e){
                        clickHandler(e,productId,productElementQuickView);
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
    var clickHandler = function(e,productId,productElementQuickView) {
        e.preventDefault();
        e.stopPropagation();
        console.log(productId)

/*

        var productQuickView = new AjaxSearchResultsItemComponent(elementsList[i],
            parentObject);
        productElementQuickView.appendChild(item.productElement);

*/
};

    init();
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