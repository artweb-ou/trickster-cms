window.AjaxSearchComponent = function(componentElement, parameters) {
	var self = this;
	var ajaxSearchResultsComponent = false;
	var inputCheckDelay = 400;
	var keyUpTimeOut;
	var searchStringLimit = 2;
	var resultsLimit = 30;

	var customResultsElement;
	var getValueCallback;
	var clickCallback;
	var resultsUpdateCallback;
	var types;
	var searchString;
	var apiMode = 'public';
	var language = '';
	var filters = '';
	var position = 'absolute';

	this.componentElement = null;
	this.inputElement = null;

	var init = function() {
		self.componentElement = componentElement;
		self.inputElement = componentElement;
		self.inputElement.autocomplete = "off";
		if (typeof parameters !== 'undefined') {
			parseParameters(parameters);
		}
		ajaxSearchResultsComponent = new AjaxSearchResultsComponent(self, customResultsElement);
		self.inputElement.addEventListener('keydown', keyPressHandler);
		self.inputElement.addEventListener('paste', pasteHandler);

		if (self.inputElement.parentElement.className === "ajaxselect_container" || self.inputElement.parentElement.className === "ajaxitemsearch_container") {
			self.inputElement.addEventListener('focus', function() {
				var container = self.inputElement.parentElement;
				container.style.border = '1px solid #6bbbff';
			});
			self.inputElement.addEventListener('focusout', function() {
				var container = self.inputElement.parentElement;
				container.style.border = '1px solid #e2e2e5';
			});
		}

		if (!customResultsElement) {
			window.addEventListener('click', windowClickHandler);
		}
		controller.addListener('ajaxSearchResultsReceived', updateData);

	};
	var parseParameters = function(parameters) {
		if (typeof parameters.clickCallback !== 'undefined') {
			clickCallback = parameters.clickCallback;
		}
		if (typeof parameters.resultsUpdateCallback !== 'undefined') {
			resultsUpdateCallback = parameters.resultsUpdateCallback;
		}
		if (typeof parameters.getValueCallback !== 'undefined') {
			getValueCallback = parameters.getValueCallback;
		}
		if (typeof parameters.types !== 'undefined') {
			types = parameters.types;
		}
		if (typeof parameters.apiMode !== 'undefined') {
			apiMode = parameters.apiMode;
		}
		if (typeof parameters.searchStringLimit !== 'undefined') {
			searchStringLimit = parseInt(parameters.searchStringLimit, 10);
		}
		if (typeof parameters.resultsLimit !== 'undefined') {
			resultsLimit = parseInt(parameters.resultsLimit, 10);
		}
		if (typeof parameters.language !== 'undefined') {
			language = parameters.language;
		}
		if (typeof parameters.filters !== 'undefined') {
			filters = parameters.filters;
		}
		if (typeof parameters.position !== 'undefined') {
			position = parameters.position;
		}
		if (typeof parameters.customResultsElement != 'undefined') {
			customResultsElement = parameters.customResultsElement;
		}
	};
	var pasteHandler = function(event) {
		checkInput();
	};
	var keyPressHandler = function(event) {
		if (event.keyCode == '40') {
			window.eventsManager.preventDefaultAction(event);
			ajaxSearchResultsComponent.setNextOption();
		} else if (event.keyCode == '38') {
			window.eventsManager.preventDefaultAction(event);
			ajaxSearchResultsComponent.setPreviousOption();
		} else if (event.keyCode == '35') {
			window.eventsManager.preventDefaultAction(event);
			ajaxSearchResultsComponent.setLastOption();
		} else if (event.keyCode == '36') {
			window.eventsManager.preventDefaultAction(event);
			ajaxSearchResultsComponent.setFirstOption();
		} else if (event.keyCode == '13') {
			window.eventsManager.preventDefaultAction(event);
			if (ajaxSearchResultsComponent.openOption()) {
				window.eventsManager.cancelBubbling(event);
			}
		} else {
			checkInput();
		}
	};
	var windowClickHandler = function() {
		ajaxSearchResultsComponent.hideComponent();
	};
	var checkInput = function() {
		window.clearTimeout(keyUpTimeOut);
		keyUpTimeOut = window.setTimeout(function() {
			if (getValueCallback) {
				searchString = getValueCallback();
			} else {
				searchString = self.inputElement.value;
			}
			searchString = searchString.replace(/^\s+/, '').replace(/\s+$/, ''); // trim
			if (types && searchString.length > searchStringLimit) {
				ajaxSearchLogics.sendQuery(updateData, encodeURIComponent(searchString), types, apiMode);
			}
		}, inputCheckDelay);
	};
	var updateData = function(responseData) {
		var allElements = [];
		for (var type in responseData) {
			for (var i = 0; i < responseData[type].length; i++) {
				if (typeof (responseData[type][i]['searchTitle']) !== 'undefined') {
					responseData[type][i].title = responseData[type][i]['searchTitle'];
				}
			}

			responseData[type].sort(function(a, b) {
				var aTitle = a.title.toUpperCase();
				var bTitle = b.title.toUpperCase();
				var keyword = searchString.toUpperCase();
				var aIndex = aTitle.indexOf(keyword);
				var bIndex = bTitle.indexOf(keyword);
				if (aIndex == bIndex) {
					return aTitle > bTitle;
				} else if (aIndex < 0) {
					return true;
				} else if (bIndex < 0) {
					return false;
				} else if (aIndex == 0) {
					return false;
				} else if (bIndex == 0) {
					return true;
				}

				return aTitle > bTitle;
			});
			allElements = allElements.concat(responseData[type]);
		}
		ajaxSearchResultsComponent.setSelectedIndex(false);
		if (allElements.length > 0) {
			ajaxSearchResultsComponent.updateData(allElements);
			ajaxSearchResultsComponent.displayComponent();
		} else {
			ajaxSearchResultsComponent.hideComponent();
		}
		if (resultsUpdateCallback) {
			resultsUpdateCallback(allElements);
		}
	};
	this.setFilters = function(filterString) {
		filters = filterString;
	};
	this.getPosition = function() {
		return position;
	};
	this.clickHandler = function(data) {
		ajaxSearchResultsComponent.hideComponent();
		if (typeof clickCallback == 'function') {
			clickCallback(data);
		}
	};

	this.setTypes = function(newTypes) {
		types = newTypes;
	};

	init();
};
window.AjaxSearchResultsComponent = function(parentObject, customResultsElement) {
	var componentElement;
	var contentElement;
	var resultItems = [];
	var selectedIndex = false;
	var self = this;
	var init = function() {
		if (customResultsElement) {
			componentElement = customResultsElement;
		} else {
			componentElement = self.makeElement('div', 'ajaxsearch_results_block');
		}
		componentElement.addEventListener('click', clickHandler);

		contentElement = document.createElement('div');
		contentElement.className = 'ajaxsearch_results_list';
		componentElement.appendChild(contentElement);

		if (!customResultsElement) {
			document.body.appendChild(componentElement);
		}
		window.eventsManager.addHandler(window, 'resize', refreshStatus);
	};

	this.updateData = function(elementsList) {
		resultItems = [];
		while (contentElement.firstChild) {
			contentElement.removeChild((contentElement.firstChild));
		}
		for (var i = 0; i < elementsList.length; i++) {
			var item = new AjaxSearchResultsItemComponent(elementsList[i], parentObject);
			contentElement.appendChild(item.componentElement);

			resultItems.push(item);
		}
	};
	this.displayComponent = function() {
		componentElement.style.visibility = 'hidden';
		componentElement.style.display = 'block';
		componentElement.style.position = parentObject.getPosition();
		refreshStatus();
		componentElement.style.visibility = 'visible';
	};
	this.hideComponent = function() {
		componentElement.style.display = 'none';
	};

	this.setFirstOption = function() {
		if (resultItems.length > 0) {
			self.setSelectedIndex(0);
		}
	};
	this.setLastOption = function() {
		if (resultItems.length > 0) {
			self.setSelectedIndex(resultItems.length - 1);
		}
	};
	this.setNextOption = function() {
		if (selectedIndex !== false) {
			var nextOptionNumber = selectedIndex + 1;
			if (nextOptionNumber < resultItems.length) {
				self.setSelectedIndex(nextOptionNumber);
			}
		} else {
			self.setFirstOption();
		}
	};
	this.setPreviousOption = function() {
		if (selectedIndex !== false) {
			var previousOptionNumber = selectedIndex - 1;
			if (previousOptionNumber >= 0) {
				self.setSelectedIndex(previousOptionNumber);
			}
		}
	};
	this.setSelectedIndex = function(newSelectedIndex) {
		selectedIndex = newSelectedIndex;
		for (var i = 0; i < resultItems.length; i++) {
			if (i === selectedIndex) {
				resultItems[i].setActive(true);
			} else {
				resultItems[i].setActive(false);
			}
		}
	};
	this.openOption = function() {
		if (typeof resultItems[selectedIndex] !== 'undefined') {
			resultItems[selectedIndex].click();
			return true;
		}
		return false;
	};
	var refreshStatus = function() {
		if (!customResultsElement) {
			var inputPositions = domHelper.getElementPositions(parentObject.inputElement.parentElement);
			var inputLeft = inputPositions.x;
			var inputTop = inputPositions.y;
			var inputHeight = parentObject.inputElement.offsetHeight;
			var inputWidth = parentObject.componentElement.parentElement.offsetWidth;
			var leftPosition = (inputLeft);
			var topPosition = (inputTop + inputHeight);

			componentElement.style.left = leftPosition + 'px';
			componentElement.style.top = topPosition - 2 + 'px';
			componentElement.style.width = inputWidth - 2 + 'px';
		}
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
	};
	init();
};
DomElementMakerMixin.call(AjaxSearchResultsComponent.prototype);

window.AjaxSearchResultsItemComponent = function(data, parentObject) {
	var self = this;
	var componentElement;

	this.componentElement = null;

	var init = function() {
		if (typeof data.url !== 'undefined') {
			componentElement = document.createElement('a');
			componentElement.href = data.url;
		} else {
			componentElement = document.createElement('span');
		}

		componentElement.className = 'ajaxsearch_results_item';
		var title = data.title;
		if (typeof data.language !== 'undefined') {
			title = title + " (" + data.language + ") ";
		}

		if (typeof data.structureType !== 'undefined') {
			componentElement.innerHTML = '<span class=\"icon icon_' + data.structureType + '\"></span><span class="ajaxsearch_results_item_text">' + title + '</span>';
		} else {
			componentElement.innerHTML = '<span class="ajaxsearch_results_item_text">' + title + '</span>';
		}
		componentElement.addEventListener('mouseup', clickHandler);

		self.componentElement = componentElement;
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		parentObject.clickHandler(data);
	};
	this.click = function() {
		parentObject.clickHandler(data);
	};
	this.setActive = function(active) {
		if (active == true) {
			componentElement.className = 'ajaxsearch_results_item ajaxsearch_results_active';
		} else {
			componentElement.className = 'ajaxsearch_results_item';
		}
	};

	init();
};