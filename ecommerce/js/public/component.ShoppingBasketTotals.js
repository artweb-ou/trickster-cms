window.ShoppingBasketTotalsComponent = function(componentElement) {
    var deliveryRow, vatlessRow, vatRow, totalRow, productsFullPrice, pricesIncludeVatRow;
    var discountRows = {}, serviceRows = {};

    var init = function() {
        productsFullPrice = createRow('productsfullprice', translationsLogics.get('shoppingbasket.productstable_productsprice'));
        deliveryRow = createRow('delivery', translationsLogics.get('shoppingbasket.deliveryprice'));
        if (shoppingBasketLogics.displayVat) {
            vatlessRow = createRow('vatless', translationsLogics.get('shoppingbasket.vatlesstotalprice'));
            vatRow = createRow('vat', translationsLogics.get('shoppingbasket.vatamount'));
            totalRow = createRow('total', translationsLogics.get('shoppingbasket.totalprice'));
        } else {
            totalRow = createRow('total', translationsLogics.get('shoppingbasket.totalprice'));
            pricesIncludeVatRow = createRow('pricesincludevat', translationsLogics.get('shoppingbasket.pricesincludevat'));
        }
    };
    var createRow = function(typeName, title) {
        var row = new ShoppingBasketTotalsRowComponent(typeName);
        row.setTitle(title);
        componentElement.appendChild(row.getComponentElement());
        return row;
    };
    var createDiscountRow = function(discountInfo) {
        var row = new ShoppingBasketTotalsRowComponent('discount');
        row.setTitle(discountInfo.title);
        row.setPrice(-discountInfo.amount);
        if (shoppingBasketLogics.displayVat) {
            componentElement.insertBefore(row.getComponentElement(), vatlessRow.getComponentElement());
        } else {
            componentElement.insertBefore(row.getComponentElement(), totalRow.getComponentElement());
        }

        discountRows[discountInfo.code] = row;
        return row;
    };
    var createServiceRow = function(serviceInfo) {
        var row = new ShoppingBasketTotalsRowComponent('service');
        row.setTitle(serviceInfo.title);
        row.setPrice(serviceInfo.price);
        componentElement.insertBefore(row.getComponentElement(), deliveryRow.getComponentElement());

        serviceRows[serviceInfo.id] = row;
        return row;
    };
    this.updateData = function() {
        productsFullPrice.setPrice(shoppingBasketLogics.productsPrice);
        deliveryRow.setPrice(shoppingBasketLogics.deliveryPrice);
        var deliveryTitle = translationsLogics.get('shoppingbasket.deliveryprice');
        var selectedDelivery = shoppingBasketLogics.getSelectedDeliveryType();
        if (selectedDelivery) {
            deliveryTitle += ' (' + selectedDelivery.title + ')';
        }
        deliveryRow.setTitle(deliveryTitle);
        if (shoppingBasketLogics.displayVat) {
            vatlessRow.setPrice(shoppingBasketLogics.vatLessTotalPrice);
            vatRow.setPrice(shoppingBasketLogics.vatAmount);
        }

        totalRow.setPrice(shoppingBasketLogics.totalPrice);

        var discountsList = shoppingBasketLogics.getDiscountsList();
        var usedDiscountCodesMap = {};
        for (var i = 0; i < discountsList.length; ++i) {
            var discountInfo = discountsList[i];
            if (typeof discountRows[discountInfo.code] == 'undefined') {
                createDiscountRow(discountInfo);
            } else {
                discountRows[discountInfo.code].setTitle(discountInfo.title);
                discountRows[discountInfo.code].setPrice(-discountInfo.amount);
            }
            usedDiscountCodesMap[discountInfo.code] = true;
        }
        for (var code in discountRows) {
            if (typeof usedDiscountCodesMap[code] == 'undefined') {
                componentElement.removeChild(discountRows[code].getComponentElement());
                delete (discountRows[code]);
            }
        }

        var services = shoppingBasketLogics.getSelectedServices();
        var usedServiceIdsMap = {};
        for (var j = 0; j < services.length; ++j) {
            var serviceInfo = services[j];
            if (typeof serviceRows[serviceInfo.id] == 'undefined') {
                createServiceRow(serviceInfo);
            }
            usedServiceIdsMap[serviceInfo.id] = true;
        }
        for (var id in serviceRows) {
            if (typeof usedServiceIdsMap[id] == 'undefined') {
                componentElement.removeChild(serviceRows[id].getComponentElement());
                delete (serviceRows[id]);
            }
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
