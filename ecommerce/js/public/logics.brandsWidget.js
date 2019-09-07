window.brandsWidgetLogics = new function() {
    var brandsList = false;

    var initLogics = function() {
        if (typeof window.brandsWidget != 'undefined' && typeof window.brandsWidget.brands != 'undefined' && window.brandsWidget.brands.length > 0) {
            brandsList = window.brandsWidget.brands;
            controller.addListener('DOMContentReady', initComponents);
        }
    };
    var initComponents = function() {
        var elements = _('.brands_widget');
        for (var i = 0; i < elements.length; i++) {
            new BrandsWidgetComponent(elements[i]);
        }
    };
    this.getBrandsList = function() {
        return brandsList;
    };
    controller.addListener('initLogics', initLogics);
};