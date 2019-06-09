window.PaymentFormLogics = new function() {
    var initComponents = function() {
        var element = _('.payment_form')[0];
        if (element) {
            new PaymentFormComponent(element);
        }
    };
    controller.addListener('initDom', initComponents);
};