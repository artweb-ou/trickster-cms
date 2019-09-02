window.pagerLogics = new function() {
	var initComponents = function() {
		var pagerBlock = _('.pager_block')[0];
		if (pagerBlock.querySelectorAll('.pager_page').length >0 ) {

			[].forEach.call(pagerBlock.querySelectorAll('.pager_page'), function(pagerPage, i) {
				new PagerComponent(pagerPage, i);
			});

		//	new PagerComponent(pagerBlock);
		}
	};
	controller.addListener('initDom', initComponents);

	// pager_page

	// this.getPager = function(baseURL, elementsCount, elementsOnPage, currentPage, parameterName, visibleAmount) {
	// 	if (typeof elementsOnPage == 'undefined' || !elementsOnPage) {
	// 		elementsOnPage = 10;
	// 	}
	// 	if (typeof currentPage == 'undefined' || !currentPage) {
	// 		currentPage = 0;
	// 	}
	// 	if (typeof parameterName == 'undefined' || !parameterName) {
	// 		parameterName = 'page';
	// 	}
	// 	if (typeof visibleAmount == 'undefined' || !visibleAmount) {
	// 		visibleAmount = 1;
	// 	}
	// 	console.log(elementsCount)
	// 	return new PagerComponent(new PagerData(baseURL, elementsCount, elementsOnPage, currentPage, parameterName, visibleAmount));
	// };

	//pager_block
};
window.PagerData = function(baseURL, elementsCount, elementsOnPage, currentPage, parameterName, visibleAmount) {
	var self = this;

	this.nextPage = {};
	this.pagesList = new Array();
	this.previousPage = {};
	this.currentPage = 0;
	this.startElement = 0;
	this.pagesAmount = 0;

	var init = function() {
		self.pagesAmount = Math.ceil(elementsCount / elementsOnPage);
		self.currentPage = currentPage;

		if (self.currentPage > self.pagesAmount) {
			self.currentPage = self.pagesAmount;
		} else if (self.currentPage < 1) {
			self.currentPage = 1;
		}

		self.startElement = (self.currentPage - 1) * elementsOnPage;

		self.previousPage['active'] = false;
		self.previousPage['text'] = '';
		self.previousPage['URL'] = '';
		self.previousPage['selected'] = false;
		if (self.currentPage != 1) {
			self.previousPage['number'] = self.currentPage - 1;
			self.previousPage['active'] = true;
			self.previousPage['URL'] = baseURL + 'page:' + (self.currentPage - 1) + '/';
		}
		self.nextPage['active'] = false;
		self.nextPage['text'] = '';
		self.nextPage['URL'] = '';
		self.nextPage['selected'] = false;
		if (self.currentPage != self.pagesAmount) {
			self.nextPage['number'] = self.currentPage + 1;
			self.nextPage['active'] = true;
			self.nextPage['URL'] = baseURL + 'page:' + (self.currentPage + 1) + '/';
		}

		var start = self.currentPage - visibleAmount;
		var end = self.currentPage + visibleAmount;

		if (self.currentPage <= visibleAmount + 2) {
			end = visibleAmount * 2 + 3;
		}

		if (self.currentPage >= self.pagesAmount - visibleAmount - 2) {
			start = self.pagesAmount - visibleAmount * 2 - 2;
		}

		if (start < 1) {
			start = 1;
		}
		if (end > self.pagesAmount) {
			end = self.pagesAmount;
		}

		if (start > 1) {
			self.pagesList.push(createPageElement(1));
		}
		if (start > 2) {
			self.pagesList.push(createPageElement('...'));
		}

		for (var i = start; i <= end; i++) {
			self.pagesList.push(createPageElement(i));
		}

		if (end < self.pagesAmount - 1) {
			self.pagesList.push(createPageElement('...'));
		}
		if (end < self.pagesAmount) {
			self.pagesList.push(createPageElement(self.pagesAmount));
		}
	};
	var createPageElement = function(number) {
		var element = {};
		if (!isNaN(number)) {
			element['text'] = number;
			element['number'] = number;
			element['active'] = true;
			element['URL'] = baseURL + 'page:' + number + '/';
			element['selected'] = element['number'] == self.currentPage;
		} else {
			element['text'] = '...';
			element['number'] = false;
			element['active'] = false;
			element['URL'] = false;
			element['selected'] = false;
		}
		return element;
	};
	init();
};