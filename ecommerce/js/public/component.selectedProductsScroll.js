window.SelectedProductsScrollComponent = function(componentElement) {
	var pagesCount = 0;
	var containerElement;
	var currentPage = 0;
	var pageWidth = 0;
	var effectSpeed = 2000;
	var pageDuration = 4000;
	var interval;
	var resizeTimeout;
	var previousWidth = 0;
	var autoScroll = false;
	var init = function() {
		var element;
		if (containerElement = componentElement.querySelector('.selectedproducts_scroll')) {

			element = componentElement.querySelector('.selectedproducts_scrollbutton_left');
			if (element) {
				element.addEventListener('click', previousClick);
			}
			element = componentElement.querySelector('.selectedproducts_scrollbutton_right');
			if (element) {
				element.addEventListener('click', nextClick);
			}
			reset();
			showPage(currentPage);
			window.addEventListener('resize', resize);
		}
	};
	var showNextPage = function() {
		if (currentPage < pagesCount) {
			currentPage++;
		} else {
			currentPage = 0;
		}
		showPage(currentPage);
	};
	var showPreviousPage = function() {
		if (currentPage > 0) {
			currentPage--;
		} else {
			currentPage = pagesCount - 1;
		}
		showPage(currentPage);
	};
	var showPage = function(page) {
		var endScrollLeft = page * pageWidth;
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
		showPage(currentPage);

		pagesCount = containerElement.scrollWidth / containerElement.offsetWidth;
		pageWidth = containerElement.offsetWidth;
		if (autoScroll) {
			interval = setInterval(showNextPage, pageDuration);
		}
	};
	init();
};