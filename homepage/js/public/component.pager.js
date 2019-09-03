window.PagerComponent = function(pagerPage, pageIndex, selectedPageIndex) {
	var self = this;
	this.pagerPage = false;
	this.urlParameters = [];
	var currentPage;

	var data = [];
	data['number'] = 2;

	var init = function() {
		if (!selectedPageIndex) {
			selectedPageIndex = 0;
		}
		eventsManager.addHandler(pagerPage, 'click', self.getChangePage(selectedPageIndex));
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
	var changePage = function(event) {
		eventsManager.preventDefaultAction(event);
		console.log(pagerPage.text)
		window.urlParameters.setParameter('page', pagerPage.text);
		pagerPage.className += ' pager_active';

		//     setParameter(namespaceURI: string, localName: string, value: any): void;
		// http://klaasistuudio.local/est/peegel/limit:5/sort:price/page:2/
	};
	this.getChangePage = function(selectedPageIndex) {
		pagerPage[selectedPageIndex].classList.remove('pager_active');
		pagerPage.className += ' pager_active';
		return changePage;
	};
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
