window.SelectedProductsColumnComponent = function(componentElement) {
    var self = this;
    var init = function() {
        var containerElement = _('.selectedproducts_column_products', componentElement)[0];
        var imageElements = _('.slide', containerElement);
        self.initSlides({
            'componentElement': containerElement,
            'slideElements': imageElements,
            'interval': 6000,
            'changeDuration': 2,
            'heightCalculated': true,
            'sp_autoStart': true,
        });
    };
    init();
};
SlidesMixin.call(SelectedProductsColumnComponent.prototype);