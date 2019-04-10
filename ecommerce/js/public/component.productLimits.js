window.ProductLimitsComponent = function(componentElement) {
    var limitElement;

    var init = function() {
        limitElement = componentElement.querySelector('select.products_limit_dropdown');
        eventsManager.addHandler(limitElement, 'change', changeLimit);
    };

    var changeLimit = function() {
        document.location.href = limitElement.value;
    };

    init();
};