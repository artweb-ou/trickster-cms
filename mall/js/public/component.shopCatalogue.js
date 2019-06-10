window.ShopCatalogueComponent = function(componentElement) {
    var layouts = {};
    var currentLayout;
    var self = this;

    var init = function() {
        var layoutsInfo = {
            'list': {
                'buttonClass': 'shop_catalogue_layout_option_list',
                'contentClass': 'shop_catalogue_shops_index',
            },
            'thumbnails': {
                'buttonClass': 'shop_catalogue_layout_option_thumbnails',
                'contentClass': 'shop_catalogue_shops_thumbnails',
            },
            'details': {
                'buttonClass': 'shop_catalogue_layout_option_details',
                'contentClass': 'shop_catalogue_shops_details',
            },
        };
        for (var layoutName in layoutsInfo) {
            var classesInfo = layoutsInfo[layoutName];
            var buttonElement = _('.' + classesInfo['buttonClass'], componentElement)[0];
            var contentElement = _('.' + classesInfo['contentClass'], componentElement)[0];
            if (buttonElement && contentElement) {
                layouts[layoutName] = new ShopCatalogueLayoutComponent(self, buttonElement, contentElement, layoutName);
            }
        }
        var initialLayout;
        var storedLayoutName = storageInterface.getValue('shopsLayout');
        if (storedLayoutName && layouts[storedLayoutName]) {
            initialLayout = layouts[storedLayoutName];
        } else {
            for (var name in layouts) {
                initialLayout = layouts[name];
                break;
            }
        }
        if (initialLayout) {
            self.activateLayout(initialLayout);
        }
    };
    this.activateLayout = function(newLayout) {
        storageInterface.setValue('shopsLayout', newLayout.getName());
        if (currentLayout && currentLayout != newLayout) {
            currentLayout.deActivate();
        }
        newLayout.activate();
        currentLayout = newLayout;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};

window.ShopCatalogueLayoutComponent = function(catalogue, buttonElement, contentElement, name) {
    var CLASS_BUTTON_SELECTED = 'shop_catalogue_layout_option_selected';
    var CLASS_CONTENT_ACTIVE = 'shop_catalogue_shops_section_active';
    var self = this;

    var init = function() {
        window.eventsManager.addHandler(buttonElement, 'click', buttonClick);
    };
    var buttonClick = function(event) {
        eventsManager.preventDefaultAction(event);
        catalogue.activateLayout(self);
    };
    this.activate = function() {
        domHelper.addClass(buttonElement, CLASS_BUTTON_SELECTED);
        domHelper.addClass(contentElement, CLASS_CONTENT_ACTIVE);
    };
    this.deActivate = function() {
        domHelper.removeClass(buttonElement, CLASS_BUTTON_SELECTED);
        domHelper.removeClass(contentElement, CLASS_CONTENT_ACTIVE);
    };
    this.getName = function() {
        return name;
    };
    init();
};