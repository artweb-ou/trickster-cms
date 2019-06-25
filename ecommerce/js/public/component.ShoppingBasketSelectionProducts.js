window.ShoppingBasketSelectionProducts = function(componentElement) {
    var self = this;
    var checkout = false;
    var rowsContainerElement = false;
    var productComponentsList = [];
    var init = function() {
        controller.addListener('startApplication', updateData);
        controller.addListener('shoppingBasketUpdated', updateData);

        checkout = componentElement.dataset.checkout;
    };
    var updateData = function() {
        var i;
        var products = window.shoppingBasketLogics.productsList;
        var usedIdIndex = {};
        for (i = 0; i < productComponentsList.length; i++) {
            productComponentsList[i].destroy();
        }
        productComponentsList = [];
        for (i = 0; i < products.length; i++) {
            var basketProductId = products[i].basketProductId;
            var product = new ShoppingBasketSelectionProduct(basketProductId, products[i]);
            productComponentsList.push(product);
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
                    if (typeof productComponentsList[i] !== 'undefined') {
                        productComponentsList[i].setComponentElement(productRows[i]);
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
