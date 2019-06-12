window.OrderFormLogics = new function() {
    var initComponents = function() {
        var element = _('.order_form')[0];
        if (element) {
            new OrderFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};