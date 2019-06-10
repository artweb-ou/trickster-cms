window.ShoppingBasketSelectionTable = function(componentElement) {

    var rowsContainerElement = false;
    var productRowsList = [];
    var productRowsIndex = {};
    var init = function() {
        rowsContainerElement = _('.shoppingbasket_table_rows', componentElement)[0];

        controller.addListener('startApplication', updateData);
        controller.addListener('shoppingBasketUpdated', updateData);
    };
    var updateData = function() {
        var products = window.shoppingBasketLogics.productsList;
        var usedIdIndex = {};
        for (var i = 0; i < products.length; i++) {
            var basketProductId = products[i].basketProductId;
            usedIdIndex[basketProductId] = true;

            var product = false;
            if (!productRowsIndex[basketProductId]) {
                product = new ShoppingBasketSelectionProduct(products[i]);
                productRowsIndex[basketProductId] = product;
                productRowsList.push(product);
                rowsContainerElement.appendChild(product.componentElement);
            } else {
                product = productRowsIndex[basketProductId];
            }

            if (product) {
                product.updateContents();
            }
        }
        for (var j = 0; j < productRowsList.length; j++) {
            var basketProductId2 = productRowsList[j].basketProductId;
            if (typeof usedIdIndex[basketProductId2] == 'undefined') {
                rowsContainerElement.removeChild(productRowsList[j].componentElement);
                delete productRowsIndex[basketProductId2];
                productRowsList.splice(j, 1);
            }
        }
    };
    init();
};
