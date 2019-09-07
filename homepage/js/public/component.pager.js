window.PagerComponent = function(pagerPage, pagerPageIndex, pagerPages) {
	var self = this;
//	this.pagerPage = false;
// 	this.urlParameters = [];
	var data = [];
	var currentPage;
	// var selectedPageIndex;
	// var pagerPage;
	// var pagerPageIndex;
	// var currentPageIndex;
	// data['number'] = 2;
	var currentPageIndex;


	var replacement;

	var init = function() {
		eventsManager.addHandler(pagerPage, 'click', getChangePage);
	};
	var getChangePage = function(event) {
		eventsManager.preventDefaultAction(event);
		window.urlParameters.setParameter('page', pagerPages[pagerPageIndex].textContent);

		currentPageIndex = getCurrentPageIndex();
		currentPage = pagerPages[currentPageIndex];

		domHelper.removeClass(currentPage, 'pager_active');
		domHelper.addClass(pagerPage, 'pager_active');

		changePagerPageTag(currentPage, currentPageIndex, 'a');
		changePagerPageTag(pagerPage, pagerPageIndex, 'span');
	};



	var getCurrentPageIndex = function() {
		var cpi = 0;
		pagerPages.forEach(function(pagerItem, pagerPage) {
			if (domHelper.hasClass(pagerItem, 'pager_active')) {
				cpi = pagerPage;
			}
		});
		return cpi;
	};



	var changePagerPageTag = function(replaced, replacedItemIndex, newTag) {

		let replacedHtml = replaced.innerHTML;
		replacement = document.createElement(newTag);

		// copy attributes
		if (replaced.hasAttributes()) {
			var attrs = replaced.attributes;
			for (var i = attrs.length - 1; i >= 0; i--) {
				replacement.setAttribute(attrs[i].name, attrs[i].value);
			}
		}

		replacement.innerHTML = replacedHtml;
		replaced.replaceWith(replacement);

		pagerPages[replacedItemIndex] = replacement;
		pagerPage = pagerPages[replacedItemIndex];
		pagerPageIndex = pagerPages.indexOf(pagerPage);

		eventsManager.addHandler(pagerPage, 'click', getChangePage);

	};


	init();
};

window.PagerPageUrl = function(listElementId, selectedPageId, productsListSetsArray, data) {
	var productListPagerLink;
	var productListPagerUrl;
	self.productElement = listElementId;

	if(productsListSetsArray.length > 0) {
		[].forEach.call(productsListSetsArray, function(productsListSet, i) {
			if (productsListSet === 'pager') {
				self.listElementId.classList += ' product_paging';
				productListPagerUrl = '/ajaxProductsList/listElementId:' + listElementId + '/page:' + selectedPageId + '/';

				productListPagerLink.href = productListPagerUrl;





				productElementQuickView = document.createElement('div');
				productElementQuickView.className = 'product_quickview_trigger';
				productElementQuickViewLink = document.createElement('a');
				productElementQuickViewLink.className = 'product_quickview_link product_quickview_button';
				productElementQuickViewUrl = '/ajaxProductsList/listElementId:' + window.currentElementId + '/elementId:' + productId + '/';
				productElementQuickViewLink.href = productElementQuickViewUrl;
				productElementQuickViewLink.innerText = translationsLogics.get('product.quickview');

				productElementQuickView.appendChild(productElementQuickViewLink);
				self.productElement.appendChild(productElementQuickView);

				productElementQuickView.addEventListener("click", function(e){
					clickHandler(e,productId,productElementQuickView,productElementQuickViewUrl);
				}, false);
			}
		});
	}
	productListPagerUrl = '/ajaxProductsList/listElementId:' + window.currentPageId + '/page:' + pageId + '/';

	productListPagerLink.href = productListPagerUrl;
	//productListPagerLink.innerText = translationsLogics.get('product.quickview');

	productElementQuickView.appendChild(productElementQuickViewLink);
	self.productElement.appendChild(productElementQuickView);

	productListPagerLink.addEventListener("click", function(e){
		clickHandler(e,pageId,productListPagerUrl);
	}, false);
}
/*
window.PagerPageComponent = function(data) {
	var componentElement;
	var self = this;
	var init = function() {
		if (data.active) {
			componentElement = document.createElement('a');
			componentElement.href = data.URL;
		} else {
			componentElement = document.createElement('span');
		}
		componentElement.className = 'pager_page';
		if (data.selected) {
			componentElement.className += ' pager_active';
		}
		componentElement.innerHTML = data.text;
		if (data.active) {
			eventsManager.addHandler(componentElement, 'click', click);
		}
	};
	var click = function(event) {
		eventsManager.preventDefaultAction(event);
		window.urlParameters.setParameter('page', data.number);
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
DomElementMakerMixin.call(PagerPageComponent.prototype);
window.PagerPreviousComponent = function(data)
{
	var componentElement;
	var self = this;
	var init = function()
	{
		componentElement= document.createElement('a');
		componentElement.className = 'pager_previous';
		if (data.active) {
			componentElement.href = data.URL;
		} else {
			componentElement.href = '';
			componentElement.className += ' pager_hidden';
		}
		componentElement.innerHTML = data.text;
		if (data.active) {
			eventsManager.addHandler(componentElement, 'click', click);
		}
	};
	var click = function(event) {
		eventsManager.preventDefaultAction(event);
		window.urlParameters.setParameter('page', data.number);
	};
	this.getComponentElement = function()
	{
		return componentElement;
	};
	init();
};
window.PagerNextComponent = function(data)
{
	var componentElement;
	var self = this;
	var init = function()
	{
		componentElement= document.createElement('a');
		componentElement.className = 'pager_next';
		if (data.active) {
			componentElement.href = data.URL;
		} else {
			componentElement.href = '';
			componentElement.className += ' pager_hidden';
		}
		componentElement.innerHTML = data.text;
		if (data.active) {
			eventsManager.addHandler(componentElement, 'click', click);
		}
	};
	var click = function(event) {
		eventsManager.preventDefaultAction(event);
		window.urlParameters.setParameter('page', data.number);
	};
	this.getComponentElement = function()
	{
		return componentElement;
	};
	init();
};
*/
