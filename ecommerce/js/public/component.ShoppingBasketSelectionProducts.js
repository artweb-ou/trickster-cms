window.ShoppingBasketSelectionProducts = function(componentElement) {
    var self = this;
    var checkout = false;
    var rowsContainerElement = false;
    var productComponentsList = [];
    var productComponentsIndex = {};
    var init = function() {
        controller.addListener('startApplication', updateData);
        controller.addListener('shoppingBasketUpdated', updateData);

        checkout = false;
        if (componentElement.dataset.checkout) {
            checkout = true;
        }
    };
    var updateData = function() {
        var products = window.shoppingBasketLogics.productsList;
        var usedIdIndex = {};
        for (var i = 0; i < products.length; i++) {
            var basketProductId = products[i].basketProductId;
            usedIdIndex[basketProductId] = true;

            var product = false;
            if (!productComponentsIndex[basketProductId]) {
                product = new ShoppingBasketSelectionProduct(basketProductId, products[i]);
                productComponentsIndex[basketProductId] = product;
                productComponentsList.push(product);
            } else {
                product = productComponentsIndex[basketProductId];
            }
        }
        for (var j = 0; j < productComponentsList.length; j++) {
            var basketProductId2 = productComponentsList[j].getBasketProductId();
            if (typeof usedIdIndex[basketProductId2] == 'undefined') {
                productComponentsIndex[basketProductId2].destroy();
                delete productComponentsIndex[basketProductId2];
                productComponentsList.splice(j, 1);
            }
        }
        buildHtml();
    };

    var buildHtml = function() {
        var templateInternal = componentElement.dataset.templateInternal;
        var compiled = new jSmart(window.templates[templateInternal]);

        componentElement.innerHTML = compiled.fetch({
            'element': self,
            'selectedCurrencyItem': window.selectedCurrencyItem,
            'checkout': checkout,
        });

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

    };
    this.getProducts = function() {
        return productComponentsList;
    };
    this.getProductsPrice = function() {
        return window.shoppingBasketLogics.getProductsPrice();
    };

    init();
};
