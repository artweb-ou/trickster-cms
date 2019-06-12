window.ShoppingBasketSelectionFormFieldHelperComponent = function(url, title) {
    var self = this;
    var componentElement;

    var init = function() {
        componentElement = self.makeElement('div', 'shoppingbasket_delivery_form_field_helper');
        var linkElement = self.makeElement('a', 'shoppingbasket_delivery_form_field_helper_link', componentElement);
        linkElement.target = '_blank';
        linkElement.href = url;
        linkElement.innerHTML = title;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(ShoppingBasketSelectionFormFieldHelperComponent.prototype);
