window.SelectedProductsScrollComponent = function(componentElement) {
    var useInactiveButtons = 1; //0 for old projects?
    var pagesCount = 0;
    var containerElement;
    var currentPage = 0;
    var pageWidth = 0;
    var effectSpeed = 1200;
    var pageDuration = 4000;
    var interval;
    var resizeTimeout;
    var previousWidth = 0;
    var autoScroll = false;
    var leftButton;
    var rightButton;
    var itemsPerPage = 0;
    var touchStartX = 0;
    var touchCurrentX = 0;
    var touchStartY = 0;
    var touchCurrentY = 0;
    var init = function() {
        if (containerElement = componentElement.querySelector('.selectedproducts_scroll')) {
            leftButton = componentElement.querySelector('.selectedproducts_scrollbutton_left');
            if (leftButton) {
                window.touchManager.addEventListener(leftButton, 'start', previousClick);
                window.touchManager.setTouchAction(leftButton, 'pan-y');
            }
            rightButton = componentElement.querySelector('.selectedproducts_scrollbutton_right');
            if (rightButton) {
                window.touchManager.addEventListener(rightButton, 'start', nextClick);
                window.touchManager.setTouchAction(rightButton, 'pan-y');
            }
            reset();
            window.addEventListener('resize', resize);
            // temporarily turned off because of iphones
            window.touchManager.setTouchAction(containerElement, 'pan-y');
            window.touchManager.addEventListener(containerElement, 'start', touchStart);
            window.touchManager.addEventListener(containerElement, 'end', touchEnd);
            window.touchManager.addEventListener(containerElement, 'cancel', touchCancel);
        }
    };
    var touchStart = function(event, touchInfo) {
        touchStartX = touchInfo.clientX;
        touchStartY = touchInfo.clientY;

        touchCurrentX = touchStartX;
        touchCurrentY = touchStartY;
        window.touchManager.addEventListener(componentElement, 'move', touchMove);
    };
    var touchCancel = function(event, touchInfo) {
        eventsManager.preventDefaultAction(event);
    };
    var touchEnd = function(event, touchInfo) {
        var limit = 5;
        var xOffset = Math.abs(touchStartX - touchCurrentX);
        var yOffset = Math.abs(touchStartY - touchCurrentY);
        if ((xOffset > limit) && (yOffset > limit)) {
            eventsManager.preventDefaultAction(event);
            if (xOffset > yOffset) {
                var difference = touchStartX - touchCurrentX;
                clearInterval(interval);

                if (difference > 0) {
                    showNextPage(1);
                } else {
                    showPreviousPage(1);
                }
            }
        }
        window.touchManager.removeEventListener(componentElement, 'move', touchMove);
    };
    var touchMove = function(event, touchInfo) {
        var difference = touchCurrentX - touchInfo.clientX;

        touchCurrentX = touchInfo.clientX;
        touchCurrentY = touchInfo.clientY;

        if (Math.abs(touchStartX - touchCurrentX) > Math.abs(touchStartY - touchCurrentY)) {
            eventsManager.preventDefaultAction(event);
            containerElement.scrollTo(containerElement.scrollLeft + difference, 0);
        }
    };

    var showNextPage = function(withoutRedirectToEnd) {
        if (typeof withoutRedirectToEnd == 'undefined') {
            withoutRedirectToEnd = useInactiveButtons;
        }
        if (currentPage < pagesCount - 1) {
            currentPage++;
            showPage(currentPage);
        } else if (!withoutRedirectToEnd) {
            currentPage = 0;
            showPage(currentPage);
        }
    };
    var showPreviousPage = function(withoutRedirectToStart) {
        if (typeof withoutRedirectToStart == 'undefined') {
            withoutRedirectToStart = useInactiveButtons;
        }
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
        } else if (!withoutRedirectToStart) {
            currentPage = pagesCount - 1;
            showPage(currentPage);
        }
    };
    var showPage = function(page) {
        if (!page) {
            domHelper.addClass(leftButton, 'selectedproducts_scrollbutton_inactive');
        } else {
            domHelper.removeClass(leftButton, 'selectedproducts_scrollbutton_inactive');
        }
        if (page === pagesCount - 1) {
            domHelper.addClass(rightButton, 'selectedproducts_scrollbutton_inactive');
        } else {
            domHelper.removeClass(rightButton, 'selectedproducts_scrollbutton_inactive');
        }
        var startItem = page * itemsPerPage;
        var endScrollLeft = containerElement.childNodes[startItem].offsetLeft; //1 pixel of justice?

        TweenLite.to(containerElement, effectSpeed / 1000, {'scrollLeft': endScrollLeft});
    };
    var previousClick = function(event) {
        event.stopPropagation();
        clearInterval(interval);
        showPreviousPage();
    };
    var nextClick = function(event) {
        event.stopPropagation();
        clearInterval(interval);
        showNextPage();
    };
    var resize = function() {
        var currentWidth = Math.floor(containerElement.offsetWidth);
        if (Math.abs(currentWidth - (previousWidth / currentWidth)) > 0.01) {
            clearTimeout(resizeTimeout);
            clearInterval(interval);

            previousWidth = currentWidth;
            resizeTimeout = setTimeout(reset, 100);
        }
    };
    var reset = function() {
        currentPage = 0;
        itemsPerPage = 0;
        pageWidth = containerElement.offsetWidth;
        var items = containerElement.childNodes;
        for (var i = 0; i < items.length; ++i) {
            var itemsWidth = items[i].offsetLeft + items[i].offsetWidth;
            if (itemsWidth <= pageWidth) {
                itemsPerPage++;
            }
        }
        pagesCount = Math.ceil(items.length / itemsPerPage);
        showPage(currentPage);
        if (autoScroll) {
            interval = setInterval(showNextPage, pageDuration);
        }
    };
    init();
};