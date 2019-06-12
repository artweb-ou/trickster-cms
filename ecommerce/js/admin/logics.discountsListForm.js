window.discountsListFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.discountslist');
        for (var i = elements.length; i--;) {
            new DiscountsListFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};