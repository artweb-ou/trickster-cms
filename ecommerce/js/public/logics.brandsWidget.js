window.brandsWidgetLogics = new function() {
    var initLogics = function() {
        if (typeof window.brandsList != 'undefined' && window.brandsList.length > 0) {
            brandsList = window.brandsList;
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
    var self = this;
    var brandsList = false;
    controller.addListener('initLogics', initLogics);
};