window.LazyImageComponent = function(componentElement) {
    var self = this;

    var url;
    var init = function() {
        url = componentElement.dataset.lazysrc;
        self.initLazyLoading({
            'componentElement': componentElement,
            'displayCallback': lazyLoadingCallback,
        });
    };
    var lazyLoadingCallback = function() {
        componentElement.src = url;
    };
    init();
};
LazyLoadingMixin.call(LazyImageComponent.prototype);