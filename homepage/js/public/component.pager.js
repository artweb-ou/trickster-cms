window.PagerComponent = function(pagerPage, pageIndex) {
	var self = this;
//	this.pagerPage = false;
	this.urlParameters = [];
	var data = [];
	// this.currentPage;
	// this.currentPageIndex;
	// data['number'] = 2;

	var init = function() {
		if (domHelper.hasClass(pagerPage, 'pager_active')) {
			this.currentPageIndex = pageIndex;
			this.currentPage = pagerPage;
			// console.log(selectedPageIndex)
		}
		eventsManager.addHandler(pagerPage, 'click', getChangePage);
		// else {
		// 	eventsManager.addHandler(pagerPage, 'click', getChangePage);
		//
		// }
		// changePage();
	//	self.getChangePage();
	};
//	pagerComponent = window.pagerLogics.getPager(window.currentElementUrl, listInfo.total, listInfo.pageSize, page, 'page', 4);


	/*
		var self = this;
		this.componentElement = false;
		var init = function() {
			var componentElement = document.createElement('div');
			componentElement.className = 'pager_block';

			var button = new PagerPreviousComponent(pagerData.previousPage);
			componentElement.appendChild(button.getComponentElement());

			for (var i = 0; i < pagerData.pagesList.length; i++) {
				var pageData = pagerData.pagesList[i];
				var page = new PagerPageComponent(pageData);
				componentElement.appendChild(page.getComponentElement());
			}
			var button = new PagerNextComponent(pagerData.nextPage);
			componentElement.appendChild(button.getComponentElement());

			self.componentElement = componentElement;
		};
	*/
	var changePage = function() {
		// window.urlParameters.setParameter('page', pagerPage.text);
//href='{$page.URL}'

		window.urlParameters.setParameter('page', pagerPage.text);
		domHelper.removeClass(currentPage, 'pager_active');
		domHelper.addClass(pagerPage, 'pager_active');
		changePagerPageTag(pagerPage,'span');
		changePagerPageTag(currentPage,'a');
		// currentPageIndex = pageIndex;
		// currentPage = pagerPage;
		this.currentPageIndex = pageIndex;
		this.currentPage = pagerPage;
		//     setParameter(namespaceURI: string, localName: string, value: any): void;
		// http://klaasistuudio.local/est/peegel/limit:5/sort:price/page:2/
	};
	var getChangePage = function(event) {
		eventsManager.preventDefaultAction(event);
		console.log(currentPage, currentPageIndex)

		if (pageIndex !== currentPageIndex) {
			// console.log(pageIndex, currentPageIndex)
			// console.log(currentPage)
			changePage();
		}

	};

	var replacePagerPageTag = function() {
		var newSelectedPage = document.createElement('a');
		newSelectedPage.innerHTML = currentPage.innerHTML;
		currentPage.parentNode.replaceChild(newSelectedPage, currentPage);
	}

	var changePagerPageTag = function(element, newTag) {
		// var newSelectedPage = document.createElement(newTag);
		// newSelectedPage.innerHTML = currentPage.innerHTML;
		// currentPage.parentNode.replaceChild(newSelectedPage, currentPage);
		//
		// var parent = document.createElement("div");
		// var child = document.createElement("p");
		// parent.appendChild(child);
		// var span = document.createElement("span");

		let elementHtml = element.innerHTML;
		let replacedElement = document.createElement(newTag);

		// copy attributes
	//	let savedAttributes = [];
		for( var i=0, attrs=element.attributes, l=attrs.length; i<l; i++) {
			replacedElement.setAttribute(attrs[i].name, attrs[i].value);
		}

		replacedElement.innerHTML = elementHtml;
		//	console.log(savedAttributes);
		console.log(replacedElement);

		// replacedElement.attributes
		// replacedElement.setAttribute(savedAttributes, 'readonly');
		element.replaceWith(replacedElement);

	//	console.log(replacedElement.outerHTML);
	};


	//	pagerPage.classList.remove('pager_active');
	//	pagerPage.className += ' pager_active';
	//	return changePage;

	//	selectedPage();
	// this.setParameter = function(name, value, ninjaUpdate) {
	// 	if (value == false) {
	// 		delete currentParameters[name];
	// 	} else {
	// 		currentParameters[name] = value;
	// 	}
	// 	if (!ninjaUpdate) {
	// 		updateHistoryState();
	// 		controller.fireEvent('urlParametersUpdate', currentParameters);
	// 	}
	// };
/*
	var click = function(event) {
		eventsManager.preventDefaultAction(event);
		window.urlParameters.setParameter('page', data.number);
	};

*/

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
