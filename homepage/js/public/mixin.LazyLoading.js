window.LazyLoadingMixin = function() {
    this.scrollCheckInterval = null;
    this.hasScrolled = false;
    this.componentElement = null;
    this.displayCallback = null;

    this.initLazyLoading = function(options) {
        this.componentElement = options.componentElement;
        this.displayCallback = options.displayCallback;
        var onScreen = checkOnScreen.bind(this)();
        if (!onScreen) {
            window.addEventListener('scroll', scrollHandler.bind(this));
            this.scrollCheckInterval = setInterval(checkIfScrolled.bind(this), 250);
        }
    };
    var scrollHandler = function() {
        this.hasScrolled = true;
    };
    var checkIfScrolled = function() {
        if (this.hasScrolled) {
            this.hasScrolled = false;
            checkOnScreen.bind(this)();
        }
    };

    var checkOnScreen = function() {
        var isOnScreen = this.isOnScreen(this.componentElement, 1.2);
        if (isOnScreen) {
            if (this.scrollCheckInterval) {
                window.removeEventListener('scroll', scrollHandler);
                clearInterval(this.scrollCheckInterval);
            }
            this.displayCallback();
        }
        return isOnScreen;
    };
    DomHelperMixin.call(this);
};
