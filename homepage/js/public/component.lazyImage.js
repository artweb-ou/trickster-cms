window.LazyImageComponent = function(componentElement) {
    var self = this;
    var init = function() {
        self.initLazyLoading({
            'componentElement': componentElement,
            'displayCallback': lazyLoadingCallback,
        });
    };
    var lazyLoadingCallback = function() {
        componentElement.classList.remove('lazy_image');
        componentElement.src = componentElement.dataset.lazysrc;
        delete componentElement.dataset.lazysrc;
        if (componentElement.dataset.lazysrcset) {
            componentElement.srcset = componentElement.dataset.lazysrcset;
            delete componentElement.dataset.lazysrcset;
        }
    };
    init();
};
LazyLoadingMixin.call(LazyImageComponent.prototype);