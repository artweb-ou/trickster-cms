window.ordersLogics = new function() {
    var ordersList = [];
    var ordersIndex = {};
    var listStartDate = false;
    var listEndDate = false;
    var PDFDownloadURL = false;
    var PDFDisplayURL = false;
    var XLSXDownloadURL = false;

    var newOrdersAmount = false;

    var filterStartDate = false;
    var filterEndDate = false;
    var filterRequestURL = false;
    var filterTypes = false;
    var deliveryTotal;
    var payedTotal;
    var totalPricesTotal;
    var initComponents = function() {
        var elements = _('.orders_list');
        for (var i = 0; i < elements.length; i++) {
            new OrdersListComponent(elements[i]);
        }
    };
    this.getPDFDownloadURL = function() {
        return PDFDownloadURL;
    };
    this.getPDFDisplayURL = function() {
        return PDFDisplayURL;
    };
    this.getXLSLDownloadUrl = function() {
        return XLSXDownloadURL;
    };
    this.getOrdersList = function() {
        return ordersList;
    };
    this.getStartDate = function() {
        return listStartDate;
    };
    this.getEndDate = function() {
        return listEndDate;
    };
    this.setFilterData = function(requestURL, startDate, endDate, types) {
        if (startDate) {
            filterStartDate = startDate;
        }
        if (endDate) {
            filterEndDate = endDate;
        }
        if (requestURL) {
            filterRequestURL = requestURL;
        }
        if (types) {
            filterTypes = types;
        }
    };
    this.filterOrders = function() {
        var parameters = {};
        parameters['start'] = filterStartDate;
        parameters['end'] = filterEndDate;
        parameters['types'] = filterTypes;
        var request = new JsonRequest(filterRequestURL, receiveData, 'ordersList', parameters);
        request.send();
    };
    this.getNewOrdersAmount = function() {
        return newOrdersAmount;
    };
    this.getDeliveryTotal = function() {
        return deliveryTotal;
    };
    this.getPayedTotal = function() {
        return payedTotal;
    };
    this.getTotalPricesTotal = function() {
        return totalPricesTotal;
    };

    var receiveData = function(responseStatus, responseName, responseData) {
        var i;
        if (responseStatus === 'success') {
            if (responseName === 'changeStatus') {
                if (typeof responseData.order != 'undefined') {
                    for (i = 0; i < responseData.order.length; i++) {
                        var id = responseData.order[i].id;
                        if (ordersIndex[id]) {
                            ordersIndex[id].updateOrderInfo(responseData.order[i]);
                        }
                    }
                }
            } else if (responseName === 'sendStatus') {

            } else {
                if (typeof responseData.orders != 'undefined') {
                    ordersList = [];
                    ordersIndex = {};
                    for (i = 0; i < responseData.orders.ordersList.length; i++) {
                        var order = new OrderData(responseData.orders.ordersList[i]);
                        ordersList.push(order);
                        ordersIndex[order.id] = order;
                    }

                    deliveryTotal = responseData.orders.deliveryTotal;
                    payedTotal = responseData.orders.payedTotal;
                    totalPricesTotal = responseData.orders.totalPricesTotal;
                    listStartDate = responseData.orders.startDate;
                    listEndDate = responseData.orders.endDate;
                    PDFDisplayURL = responseData.orders.PDFDisplayURL;
                    PDFDownloadURL = responseData.orders.PDFDownloadURL;
                    XLSXDownloadURL = responseData.orders.XLSXDownloadURL;
                    XLSXDownloadURL = responseData.orders.XLSXDownloadURL;

                    newOrdersAmount = parseInt(responseData.orders.newOrdersAmount, 10);

                    controller.fireEvent('ordersListUpdated');
                }
            }
        }
    };
    this.setStatus = function(id, newStatus) {
        if (typeof ordersIndex[id] !== 'undefined') {
            var data = ordersIndex[id];
            var parameters = {};
            var URL = data.URL.replace('admin', 'adminAjax') + 'id:' + data.id + '/action:changeStatus/orderStatus:' + newStatus;
            var request = new JsonRequest(URL, receiveData, 'changeStatus', parameters);
            request.send();
        }
    };

    this.sendStatus = function(id) {
        if (typeof ordersIndex[id] !== 'undefined') {
            var data = ordersIndex[id];

            var URL = data.URL.replace('admin', 'adminAjax') + 'id:' + data.id + '/action:sendStatusNotification/';

            var request = new JsonRequest(URL, null, 'sendStatus', null);
            request.send();
        }
    };

    controller.addListener('initDom', initComponents);
};
window.OrderData = function(data) {
    var self = this;

    this.id = false;
    this.orderNumber = false;
    this.totalAmount = false;
    this.totalPrice = false;
    this.dateCreated = false;
    this.currency = false;

    this.payedPrice = false;
    this.deliveryPrice = false;
    this.productsPrice = false;
    this.discountAmount = false;

    this.URL = false;
    this.formURL = false;
    this.deleteURL = false;
    this.payerName = false;
    this.payerFirstName = false;
    this.payerLastName = false;
    this.orderStatus = false;
    this.orderStatusText = false;

    var init = function() {
        importData(data);
    };
    var importData = function(importedData) {
        self.id = parseInt(importedData.id, 10);
        self.orderNumber = importedData.orderNumber;
        self.invoiceNumber = importedData.invoiceNumber;
        self.advancePaymentInvoiceNumber = importedData.advancePaymentInvoiceNumber;
        self.orderConfirmationNumber = importedData.orderConfirmationNumber;
        self.totalAmount = parseInt(importedData.totalAmount, 10);
        self.totalPrice = importedData.totalPrice;
        self.productsPrice = importedData.productsPrice;
        self.discountAmount = importedData.discountAmount;
        self.dateCreated = importedData.dateCreated;
        self.currency = importedData.currency;

        self.payedPrice = importedData.payedPrice;
        if (importedData.deliveryPrice !== '') {
            self.deliveryPrice = importedData.deliveryPrice;
        } else {
            self.deliveryPrice = '';
        }

        self.URL = importedData.URL;
        self.formURL = importedData.formURL;
        self.deleteURL = importedData.deleteURL;
        self.payerName = importedData.payerName;
        self.payerFirstName = importedData.payerFirstName;
        self.payerLastName = importedData.payerLastName;
        self.orderStatus = importedData.orderStatus;
        self.orderStatusText = importedData.orderStatusText;
    };
    this.updateOrderInfo = function(newData) {
        data = newData;
        importData(data);
        controller.fireEvent('orderInfoUpdated', self.id);
    };

    init();
};