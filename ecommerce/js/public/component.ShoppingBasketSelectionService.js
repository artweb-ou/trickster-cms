window.ShoppingBasketSelectionService = function(info) {
    var self = this;
    this.componentElement = null;
    var inputElement;

    var init = function() {
        var componentElement = document.createElement('label');
        componentElement.className = 'shoppingbasket_service_component';

        inputElement = document.createElement('input');
        inputElement.type = 'checkbox';
        inputElement.className = 'checkbox_placeholder';
        inputElement.value = '1';
        inputElement.checked = info.selected;
        componentElement.appendChild(inputElement);
        window.checkBoxManager.createCheckBox(inputElement);
        eventsManager.addHandler(inputElement, 'change', changeHandler);

        var titleElement = document.createElement('span');
        titleElement.className = 'shoppingbasket_service_title';
        titleElement.colSpan = 4;
        titleElement.innerHTML = info.title;
        componentElement.appendChild(titleElement);

        var priceElement = document.createElement('span');
        priceElement.className = 'shoppingbasket_service_price';
        priceElement.colSpan = 2;
        priceElement.innerHTML = info.price + ' ' + window.selectedCurrencyItem.symbol;
        componentElement.appendChild(priceElement);

        self.componentElement = componentElement;
    };
    var changeHandler = function() {
        shoppingBasketLogics.setServiceSelection(info.id, inputElement.checked);
    };

    init();
};
