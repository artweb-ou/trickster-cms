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
	var init = function() {
		if (containerElement = componentElement.querySelector('.selectedproducts_scroll')) {
			leftButton = componentElement.querySelector('.selectedproducts_scrollbutton_left');
			if (leftButton) {
				leftButton.addEventListener('click', previousClick);
			}
			rightButton = componentElement.querySelector('.selectedproducts_scrollbutton_right');
			if (rightButton) {
				rightButton.addEventListener('click', nextClick);
			}
			reset();
			window.addEventListener('resize', resize);
			window.touchManager.addEventListener(componentElement, 'start', touchStart);
			window.touchManager.addEventListener(componentElement, 'end', touchEnd);
			window.touchManager.addEventListener(componentElement, 'cancel', touchCancel);
		}
	};
	var touchStartX = 0;
	var previousMoveX = 0;
	var moveDirectionRight;
	var touchStart = function(event, touchInfo) {
		eventsManager.preventDefaultAction(event);
		touchStartX = touchInfo.clientX;
		previousMoveX = 0;
		window.touchManager.addEventListener(componentElement, 'move', touchMove);
	};
	var touchCancel = function(event, touchInfo) {
		eventsManager.preventDefaultAction(event);
		touchStartX = 0;
		previousMoveX = 0;
	};
	var touchEnd = function(event, touchInfo) {
		eventsManager.preventDefaultAction(event);
		if (touchStartX) {
			var moveDifference = Math.abs(touchStartX - touchInfo.clientX);
			if (moveDifference > 5) {
				clearInterval(interval);
				if (moveDirectionRight) {
					showNextPage(1);
				} else {
					showPreviousPage(1);
				}
			}else {
				//......
			}
		}
		previousMoveX = 0;
		window.touchManager.removeEventListener(componentElement, 'move', touchMove);
	};
	var touchMove = function(event, touchInfo) {
		eventsManager.preventDefaultAction(event);
		if (!previousMoveX) {
			previousMoveX = touchStartX;
		}
		var difference = previousMoveX - touchInfo.clientX;
		if (difference > 0) {
			moveDirectionRight = 1;
		} else {
			moveDirectionRight = 0;
		}
		containerElement.scrollTo(containerElement.scrollLeft + difference, 0);

		previousMoveX = touchInfo.clientX;
	};

	var showNextPage = function(withoutRedirectToEnd) {
		if(typeof withoutRedirectToEnd == "undefined") {
			withoutRedirectToEnd = useInactiveButtons;
		}
		if (currentPage < pagesCount - 1) {
			currentPage++;
			showPage(currentPage);
		} else if(!withoutRedirectToEnd){
			currentPage = 0;
			showPage(currentPage);
		}
	};
	var showPreviousPage = function(withoutRedirectToStart) {
		if(typeof withoutRedirectToStart == "undefined") {
			withoutRedirectToStart = useInactiveButtons;
		}
		if (currentPage > 0) {
			currentPage--;
			showPage(currentPage);
		} else if(!withoutRedirectToStart){
			currentPage = pagesCount - 1;
			showPage(currentPage);
		}
	};
	var showPage = function(page) {
		if(!page) {
			domHelper.addClass(leftButton, 'selectedproducts_scrollbutton_inactive');
		}else {
			domHelper.removeClass(leftButton, 'selectedproducts_scrollbutton_inactive');
		}
		if(page === pagesCount - 1) {
			domHelper.addClass(rightButton, 'selectedproducts_scrollbutton_inactive');
		}else {
			domHelper.removeClass(rightButton, 'selectedproducts_scrollbutton_inactive');
		}
		var startItem = page * itemsPerPage;
		var endScrollLeft = containerElement.childNodes[startItem].offsetLeft; //1 pixel of justice?

		TweenLite.to(containerElement, effectSpeed / 1000, {'scrollLeft': endScrollLeft});
	};
	var previousClick = function() {
		clearInterval(interval);
		showPreviousPage();
	};
	var nextClick = function() {
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
			if(itemsWidth <= pageWidth) {
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