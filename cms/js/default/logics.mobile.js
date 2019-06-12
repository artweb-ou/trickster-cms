window.mobileLogics = new function() {
    var currentBreakpoint;
    //todo: read them from CMS config instead of hardcode
    var breakpoints = {
        'xs': 386,
        'sm': 576,
        'md': 768,
        'lg': 992,
        'xl': 1200,
        'xxl': 1600,
    };
    var init = function() {
        window.addEventListener('resize', resizeHandler);
        resizeHandler();
    };
    var resizeHandler = function() {
        var windowWidth = window.innerWidth;
        currentBreakpoint = 'xxl';
        for (var key in breakpoints) {
            if (breakpoints.hasOwnProperty(key)) {
                if (windowWidth < breakpoints[key]) {
                    if (currentBreakpoint !== key) {
                        currentBreakpoint = key;
                        controller.fireEvent('mobileBreakpointChanged', currentBreakpoint);
                    }
                    break;
                }
            }
        }
    };

    this.getCurrentBreakpoint = function() {
        return currentBreakpoint;
    };

    this.isPhoneActive = function() {
        return (currentBreakpoint === 'xs' || currentBreakpoint === 'sm');
    };
    init();
};