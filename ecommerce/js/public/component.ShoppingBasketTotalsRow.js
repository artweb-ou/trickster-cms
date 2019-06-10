window.ShoppingBasketTotalsRowComponent = function(typeName) {
    var self = this;
    var componentElement, titleElement, valueElement;

    var init = function() {
        componentElement = self.makeElement('tr', 'shoppingbasket_total shoppingbasket_total_' + typeName);
        if (typeName == 'pricesincludevat') {
            titleElement = self.makeElement('th', 'shoppingbasket_total_title', componentElement);
            titleElement.colSpan = 6;
        } else {
            titleElement = self.makeElement('th', 'shoppingbasket_total_title', componentElement);
            titleElement.colSpan = 4;
            valueElement = self.makeElement('td', 'shoppingbasket_total_value', componentElement);
            valueElement.colSpan = 2;
        }
    };
    this.setTitle = function(newTitle) {
        if (typeName == 'pricesincludevat') {
            titleElement.innerHTML = newTitle;
        } else {
            titleElement.innerHTML = newTitle + ':';
        }

    };
    this.setPrice = function(newPrice) {
        if (newPrice !== '') {
            valueElement.innerHTML = newPrice + ' ' + window.selectedCurrencyItem.symbol;
            componentElement.style.display = '';
        } else {
            componentElement.style.display = 'none';
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(ShoppingBasketTotalsRowComponent.prototype);
