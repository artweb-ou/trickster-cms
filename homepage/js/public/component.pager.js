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
	var oldCurrentPageIndex;

	var init = function() {
		currentPageIndex = getCurrentPageIndex();
		currentPage = pagerPages[currentPageIndex];
		eventsManager.addHandler(pagerPage, 'click', getChangePage);
	};
	var changePage = function(pageNumber, pagerPageIndex, currentPageIndex) {

		window.urlParameters.setParameter('page', pageNumber);
		changePagerPageTag(pagerPages[currentPageIndex],'a');
		changePagerPageTag(pagerPages[pagerPageIndex],'span');

	};
	var getChangePage = function( event) {
		eventsManager.preventDefaultAction(event);

		currentPageIndex = getCurrentPageIndex();
		currentPage = pagerPages[currentPageIndex];

		window.urlParameters.setParameter('page', pagerPage.textContent);
		domHelper.removeClass(currentPage, 'pager_active');
		domHelper.addClass(pagerPage, 'pager_active');
		let tempcurrentPage = currentPage;
		let temppagerPage = pagerPage;

		currentPage = temppagerPage;
		//pagerPage = tempcurrentPage;
		changePagerPageTag(currentPage,'a');
		eventsManager.addHandler(replacement, 'click', getChangePage);
		console.log(currentPage)
		changePagerPageTag(pagerPage,'span');

	};



	var getCurrentPageIndex = function() {
		var cpi = 0;
		pagerPages.forEach(function(pagerItem, pagerItemIndex) {
			if (domHelper.hasClass(pagerItem, 'pager_active')) {
				cpi = pagerItemIndex;
			}
		});
		return cpi;
	};



	var changePagerPageTag = function(replaced, newTag) {
		// var newSelectedPage = document.createElement(newTag);
		// newSelectedPage.innerHTML = currentPage.innerHTML;
		// currentPage.parentNode.replaceChild(newSelectedPage, currentPage);
		//
		// var parent = document.createElement("div");
		// var child = document.createElement("p");
		// parent.appendChild(child);
		// var span = document.createElement("span");

		let elementHtml = replaced.innerHTML;
		replacement = document.createElement(newTag);

		// copy attributes
		if (replaced.hasAttributes()) {
			var attrs = replaced.attributes;
			for (var i = attrs.length - 1; i >= 0; i--) {
				replacement.setAttribute(attrs[i].name, attrs[i].value);
			}
		}

		replacement.innerHTML = elementHtml;
		replaced.replaceWith(replacement);

	};


	init();
};
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
