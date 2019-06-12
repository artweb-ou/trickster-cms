window.ShoppingBasketSelectionServices = function(componentElement) {
    var containerElement;
    var init = function() {
        if (containerElement = componentElement.querySelector('.shoppingbasket_services_list')) {
            controller.addListener('startApplication', updateContents);
            controller.addListener('shoppingBasketUpdated', updateContents);
        }
    };
    var updateContents = function() {
        var servicesList = shoppingBasketLogics.getServicesList();
        if (servicesList.length) {
            while (containerElement.firstChild) {
                containerElement.removeChild(containerElement.firstChild);
            }
            for (var i = 0; i < servicesList.length; i++) {
                var serviceComponent = new ShoppingBasketSelectionService(servicesList[i]);
                containerElement.appendChild(serviceComponent.componentElement);
            }
            domHelper.removeClass(componentElement, 'shoppingbasket_services_component_hidden');
        } else {
            domHelper.addClass(componentElement, 'shoppingbasket_services_component_hidden');
        }
    };

    init();
};
