window.BrandsWidgetComponent = function(componentElement) {
    var containerScrollLeft;
    var containerScrollLimit;
    var self = this;
    var brands = [];
    var animationTimeout;
    var containerElement;
    var rowsCreated = 0;
    var fillCompleted = false;
    var init = function() {
        self.initLazyLoading({
            'componentElement': componentElement,
            'displayCallback': lazyLoadingCallback,
        });
    };

    var lazyLoadingCallback = function() {
        if (containerElement = componentElement.querySelector('.brands_widget_content')) {
            containerElement.scrollLeft = 0;
            fillBrands();
            eventsManager.addHandler(window, 'resize', updateSizes);
        }
    };

    var fillBrands = function() {
        if (containerElement.scrollWidth <= containerElement.offsetWidth * 2 ||
            rowsCreated < 2) {
            createRow();
        } else {
            fillCompleted = true;

            window.eventsManager.addHandler(componentElement, 'mouseenter',
                pauseAnimation);
            window.eventsManager.addHandler(componentElement, 'mouseleave',
                startAnimation);
            startAnimation();
        }
    };
    var createRow = function() {
        var brandsList = window.brandsWidgetLogics.getBrandsList();

        rowsCreated++;
        for (var i = 0; i < brandsList.length; i++) {
            var brandsBannerItem = new BrandsBannerItemComponent(brandsList[i], self);
            containerElement.appendChild(brandsBannerItem.componentElement);
            brands.push(brandsBannerItem);
        }
    };
    this.checkBrands = function() {
        var result = true;
        for (var i = 0; i < brands.length; i++) {
            if (!brands[i].imageLoaded) {
                result = false;
                break;
            }
        }

        if (result) {
            fillBrands();
        }
    };

    var startAnimation = function() {
        updateSizes();

        animationTimeout = window.setTimeout(animate, 50);
    };
    var updateSizes = function() {
        containerScrollLeft = containerElement.scrollLeft;
        containerScrollLimit = containerElement.scrollWidth / rowsCreated;
    };
    var animate = function() {
        if (containerScrollLeft < containerScrollLimit) {
            containerScrollLeft += 0.33;
        } else {
            containerScrollLeft = 0;
        }
        containerElement.scrollLeft = containerScrollLeft;
        if (requestAnimationFrame) {
            animationTimeout = requestAnimationFrame(animate);
        } else {
            animationTimeout = window.setTimeout(animate, 1000 / 60);
        }

    };

    var pauseAnimation = function() {
        if (window.cancelAnimationFrame) {
            window.cancelAnimationFrame(animationTimeout);
        } else {
            window.clearTimeout(animationTimeout);
        }
    };

    init();
};
LazyLoadingMixin.call(BrandsWidgetComponent.prototype);

window.BrandsBannerItemComponent = function(brandInfo, parentComponent) {

    var imageElement;

    this.imageLoaded = false;
    this.componentElement = null;

    var init = function() {
        self.componentElement = document.createElement('a');
        self.componentElement.className = 'brands_widget_item';
        self.componentElement.href = brandInfo.URL;
        self.componentElement.title = brandInfo.title;

        imageElement = document.createElement('img');
        imageElement.className = 'brands_widget_item_image';
        imageElement.src = brandInfo.imageUrl;
        self.componentElement.appendChild(imageElement);

        window.setTimeout(checkImage, 100);
    };
    var checkImage = function() {
        if (imageElement.complete) {
            self.imageLoaded = true;
            parentComponent.checkBrands();
        } else {
            window.setTimeout(checkImage, 100);
        }
    };
    var self = this;

    init();
};