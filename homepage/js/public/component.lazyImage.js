window.LazyImageComponent = function(componentElement) {
    var self = this;
    var init = function() {
        self.initLazyLoading({
            'componentElement': componentElement,
            'displayCallback': lazyLoadingCallback,
        });
    };
    var lazyLoadingCallback = function() {
        componentElement.src = componentElement.dataset.lazysrc;
        componentElement.removeAttribute('dataset-lazysrc');
        if (componentElement.dataset.lazysrcset) {
            componentElement.srcset = componentElement.dataset.lazysrcset;
            componentElement.removeAttribute('dataset-lazysrcset');
        }
    };
    init();
};
LazyLoadingMixin.call(LazyImageComponent.prototype);