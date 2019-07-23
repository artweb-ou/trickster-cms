window.OrdersListComponent = function(componentElement) {
    var init = function() {
        var element;
        if (element = _('.orders_list_table', componentElement)[0]) {
            new OrdersListTableComponent(element);
        }
        if (element = _('.orders_list_heading', componentElement)[0]) {
            new OrdersListHeadingComponent(element);
        }
        if (element = _('.orders_list_filtration', componentElement)[0]) {
            new OrdersListFiltrationForm(element);
        }
    };
    init();
};

window.OrdersListHeadingComponent = function(componentElement) {
    var PDFDownloadLink;
    var PDFDisplayLink;
    var XLSXDownloadLink;
    var startDateElement;
    var endDateElement;
    var tableComponent;

    var init = function() {
        var dateHeading = _('.orders_list_heading_date', tableComponent)[0];
        tableComponent = document.querySelectorAll('.orders_list_table')[0];
        PDFDownloadLink = _('.orders_list_heading_downloadpdf', componentElement)[0];
        PDFDisplayLink = _('.orders_list_heading_displaypdf', componentElement)[0];
        XLSXDownloadLink = _('.orders_list_heading_exportxlsx', componentElement)[0];
        startDateElement = _('.orders_list_heading_start', dateHeading)[0];
        endDateElement = _('.orders_list_heading_end', dateHeading)[0];
        if (startDateElement && endDateElement) {
            controller.addListener('ordersListUpdated', importInformation);
        }
        var element = _('.orders_list_heading_new', componentElement)[0];
        if (element) {
            new OrdersListNewOrdersComponent(element);
        }
    };
    var importInformation = function() {
        var startDate = ordersLogics.getStartDate();
        var endDate = ordersLogics.getEndDate();
        startDateElement.innerHTML = startDate;
        endDateElement.innerHTML = endDate;

        PDFDownloadLink.href = ordersLogics.getPDFDownloadURL();
        PDFDisplayLink.href = ordersLogics.getPDFDisplayURL();
        XLSXDownloadLink.href = ordersLogics.getXLSLDownloadUrl();
    };

    init();
};

window.OrdersListNewOrdersComponent = function(componentElement) {
    var linkElement;
    var valueElement;
    var init = function() {
        linkElement = _('.orders_list_heading_new_link', componentElement)[0];
        valueElement = _('.orders_list_heading_new_value', componentElement)[0];
        if (linkElement && valueElement) {
            eventsManager.addHandler(linkElement, 'click', clickHandler);
            controller.addListener('ordersListUpdated', importInformation);
        }
    };
    var importInformation = function() {
        var ordersAmount = ordersLogics.getNewOrdersAmount();
        if (ordersAmount > 0) {
            valueElement.innerHTML = ordersAmount;

            componentElement.style.visibility = 'visible';
        } else {
            componentElement.style.visibility = 'hidden';
        }
    };
    var clickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        controller.fireEvent('ordersListDisplayNew');
    };

    init();
};

window.OrdersListTableComponent = function(componentElement) {
    var rowsElement;
    var payedTotalElement;
    var totalPriceElement;
    var deliveryTotalElement;
    var filterSelector;

    var init = function() {
        if (rowsElement = _('.content_list_item', componentElement)[0]) {
            controller.addListener('ordersListUpdated', updateOrdersList);
        }
        payedTotalElement = _('.orders_list_table_payedtotal', componentElement)[0];
        deliveryTotalElement = _('.orders_list_table_deliverytotal', componentElement)[0];
        totalPriceElement = _('.orders_list_table_totalprice', componentElement)[0];

        if (filterSelector = _('select.orders_list_table_filter', componentElement)[0]) {
            reportFilterTypes();
            eventsManager.addHandler(filterSelector, 'change', filterSelectorChangeHandler);
            controller.addListener('ordersListDisplayNew', displayNewOrders);
        }
    };
    var requestOrdersList = function() {
        window.ordersLogics.filterOrders();
    };
    var filterSelectorChangeHandler = function() {
        reportFilterTypes();
        requestOrdersList();
    };
    var reportFilterTypes = function() {
        var filterTypes = filterSelector.value;
        window.ordersLogics.setFilterData(false, false, false, filterTypes);
    };
    var displayNewOrders = function() {
        filterSelector.value = 'payed;undefined';
        eventsManager.fireEvent(filterSelector, 'change');
    };
    var updateOrdersList = function() {
        var ordersList = ordersLogics.getOrdersList();
        while (rowsElement.firstChild) {
            rowsElement.removeChild(rowsElement.firstChild);
        }

        for (var i = 0; i < ordersList.length; i++) {
            var row = new OrdersListRowComponent(i, ordersList[i]);
            rowsElement.appendChild(row.componentElement);
        }

        payedTotalElement.innerHTML = ordersLogics.getPayedTotal() + ' €';
        deliveryTotalElement.innerHTML = ordersLogics.getDeliveryTotal() + ' €';
        totalPriceElement.innerHTML = ordersLogics.getTotalPricesTotal() + ' €';
    };

    init();
};

window.OrdersListRowComponent = function(number, orderData) {
    var self = this;
    this.componentElement = null;
    var ordererCell;
    var numberCell;
    var orderNumberCell;
    var orderLink;
    var payedPriceCell;
    var totalPriceCell;
    var deliveryPriceCell;
    var discountCell;
    var productsPriceCell;
    var statusCell;
    var statusChangeCell;
    var statusChangeButton;
    var dateCell;
    var removeCell;
    var removeButton;

    var init = function() {
        createDomStructure();
        refreshContents();
        controller.addListener('orderInfoUpdated', orderInfoUpdatedHandler);
    };
    var createDomStructure = function() {
        self.componentElement = document.createElement('tr');

        numberCell = document.createElement('td');
        numberCell.className = 'orders_list_row_cellnumber';
        self.componentElement.appendChild(numberCell);

        orderNumberCell = document.createElement('td');
        orderNumberCell.className = 'name_column';
        self.componentElement.appendChild(orderNumberCell);

        orderLink = document.createElement('a');
        orderLink.className = 'orders_list_row_orderlink';
        orderNumberCell.appendChild(orderLink);

        ordererCell = document.createElement('td');
        ordererCell.className = 'orders_list_row_cellorderer';
        self.componentElement.appendChild(ordererCell);

        payedPriceCell = document.createElement('td');
        payedPriceCell.className = 'orders_list_row_payedprice';
        self.componentElement.appendChild(payedPriceCell);

        deliveryPriceCell = document.createElement('td');
        deliveryPriceCell.className = 'orders_list_row_deliveryprice';
        self.componentElement.appendChild(deliveryPriceCell);

        discountCell = document.createElement('td');
        discountCell.className = 'orders_list_row_discount';
        self.componentElement.appendChild(discountCell);

        productsPriceCell = document.createElement('td');
        productsPriceCell.className = 'orders_list_row_productsprice';
        self.componentElement.appendChild(productsPriceCell);

        totalPriceCell = document.createElement('td');
        totalPriceCell.className = 'orders_list_row_totalprice';
        self.componentElement.appendChild(totalPriceCell);

        statusCell = document.createElement('td');
        statusCell.className = 'orders_list_row_status';
        self.componentElement.appendChild(statusCell);

        statusChangeCell = document.createElement('td');
        statusChangeCell.className = 'orders_list_row_statuschange';
        self.componentElement.appendChild(statusChangeCell);

        statusChangeButton = new StatusChangeButtonComponent(orderData);
        statusChangeCell.appendChild(statusChangeButton.componentElement);

        dateCell = document.createElement('td');
        dateCell.className = 'orders_list_row_date';
        self.componentElement.appendChild(dateCell);

        removeCell = document.createElement('td');
        removeCell.className = 'orders_list_row_remove';
        self.componentElement.appendChild(removeCell);

        removeButton = new ContentListRemoveButton('orders_list_row_remove_button');
        removeCell.appendChild(removeButton.componentElement);
    };
    var refreshContents = function() {
        numberCell.innerHTML = number + 1;
        orderLink.innerHTML = orderData.orderNumber;
        ordererCell.innerHTML = orderData.payerFirstName + ' ' + orderData.payerLastName;
        payedPriceCell.innerHTML = orderData.payedPrice + ' ' + orderData.currency;
        if (orderData.deliveryPrice !== '') {
            deliveryPriceCell.innerHTML = orderData.deliveryPrice + ' ' + orderData.currency;
        } else {
            deliveryPriceCell.innerHTML = '';
        }
        discountCell.innerHTML = orderData.discountAmount + ' ' + orderData.currency;
        productsPriceCell.innerHTML = orderData.productsPrice + ' ' + orderData.currency;
        totalPriceCell.innerHTML = orderData.totalPrice + ' ' + orderData.currency;
        statusCell.innerHTML = orderData.orderStatusText;

        dateCell.innerHTML = orderData.dateCreated;

        self.componentElement.className = 'content_list_item content_list_item_' + orderData.orderStatus;
        statusChangeButton.setStatus(orderData.orderStatus);
        removeButton.setURL(orderData.deleteURL);
        orderLink.href = orderData.formURL;
    };
    var orderInfoUpdatedHandler = function(id) {
        if (id == orderData.id) {
            refreshContents();
        }
    };
    init();
};

window.OrdersListFiltrationForm = function(componentElement) {
    var formElement;
    var startInput;
    var endInput;

    var presetSelector;
    var startDate;
    var endDate;
    var requestURL;

    var init = function() {
        if (formElement = _('.orders_list_filtration_form', componentElement)[0]) {
            requestURL = formElement.getAttribute('action').replace('/admin/', '/adminAjax/');

            if (presetSelector = _('select.orders_list_filtration_preset', componentElement)[0]) {
                eventsManager.addHandler(presetSelector, 'change', presetSelectorChangeHandler);
            }
            startInput = _('.orders_list_filtration_start', componentElement)[0];
            endInput = _('.orders_list_filtration_end', componentElement)[0];
            if (startInput && endInput) {
                startInput.onchange = inputsChangeHandler;
                endInput.onchange = inputsChangeHandler;
                controller.addListener('startApplication', startApplicationHandler);

                reportInputsState();
            }
        }
    };
    var startApplicationHandler = function() {
        if (presetSelector && startInput.value == '' && endInput.value == '') {
            presetSelectorChangeHandler();
        } else if (startInput.value != '' && endInput.value != '') {
            inputsChangeHandler();
        }
    };
    var presetSelectorChangeHandler = function() {
        var startDate = presetSelector.value.split('-')[0];
        var endDate = presetSelector.value.split('-')[1];
        startInput.value = startDate;
        endInput.value = endDate;

        inputsChangeHandler();
    };
    var reportInputsState = function() {
        startDate = startInput.value;
        endDate = endInput.value;

        window.ordersLogics.setFilterData(requestURL, startDate, endDate);
    };
    var inputsChangeHandler = function() {
        reportInputsState();
        if (startDate != '' && endDate != '') {
            window.ordersLogics.filterOrders(requestURL, startDate, endDate);
        }
    };

    init();
};

window.ContentListRemoveButton = function(extraClassName) {
    var self = this;
    this.componentElement = null;

    var init = function() {
        self.componentElement = document.createElement('a');
        var className = 'icon icon_delete';
        if (extraClassName) {
            className = className + ' ' + extraClassName;
        }
        self.componentElement.className = className;

        eventsManager.addHandler(self.componentElement, 'click', clickHandler);
    };
    this.setURL = function(URL) {
        self.componentElement.href = URL;
    };
    var clickHandler = function(event) {
        eventsManager.preventDefaultAction(event);
        document.location.href = self.componentElement.href;
    };

    init();
};
window.StatusChangeButtonComponent = function(orderData) {
    var self = this;
    this.componentElement = null;
    var status;
    var init = function() {
        self.componentElement = document.createElement('span');
        self.componentElement.className = 'button primary_button narrow-sides-button';
        self.componentElement.style.display = 'none';
        eventsManager.addHandler(self.componentElement, 'click', clickHandler);
    };
    this.setStatus = function(newStatus) {
        status = newStatus;
        refreshContents();
    };
    var refreshContents = function() {

        var sentStatusPossible = status === 'payed' || status === 'paid_partial';
        var paidStatusPossible = status === 'undefined';

        if (!paidStatusPossible && !sentStatusPossible) {
            hide();
        } else {
            if (sentStatusPossible) {
                self.componentElement.innerHTML = translationsLogics.get('orders.statuschange_payed') + '<br>' + translationsLogics.get('orders.statuschange_and_send_notification');
            } else {
                self.componentElement.innerHTML = translationsLogics.get('orders.statuschange_undefined') + '<br>' + translationsLogics.get('orders.statuschange_and_send_notification');
            }
            display();
        }
    };
    var clickHandler = function() {
        if (status === 'payed') {
            ordersLogics.setStatus(orderData.id, 'sent');
        } else if (status === 'undefined') {
            ordersLogics.setStatus(orderData.id, 'payed');
        }
    };
    var display = function() {
        self.componentElement.style.display = 'inline-flex';
    };
    var hide = function() {
        self.componentElement.style.display = 'none';
    };
    init();
};
