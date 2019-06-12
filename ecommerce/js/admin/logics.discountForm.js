window.DiscountFormLogics = new function() {
    var initComponents = function() {
        var element = _('.discount_form')[0];
        if (element) {
            new DiscountFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};