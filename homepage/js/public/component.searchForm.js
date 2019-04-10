function SearchFormComponent(formElement) {
	var self = this;

	this.submitButton = null;
	this.formElement = null;
	var inputElement;

	var init = function() {
		self.formElement = formElement;
		if (self.submitButton = _('.search_button', formElement)[0]) {
			eventsManager.addHandler(self.submitButton, 'click', self.submitForm);
		}
		eventsManager.addHandler(self.formElement, 'submit', self.submitForm);
		eventsManager.addHandler(self.formElement, 'keydown', self.checkKey);

		var allowedSearchTypes;
		if (formElement.hasAttribute('data-types') && formElement.getAttribute('data-types') != '') {
			allowedSearchTypes = formElement.getAttribute('data-types');
		} else {
			allowedSearchTypes = 'product,category,news,article,folder,discount';
		}
		inputElement = formElement.querySelector('.ajaxsearch_input');
		if (inputElement && inputElement.className.indexOf('ajaxsearch_input') != -1) {
			var parameters = {
				'clickCallback': ajaxSearchResultClick,
				'apiMode': 'public',
				'searchStringLimit': 1,
				'types': allowedSearchTypes
			};
			new AjaxSearchComponent(inputElement, parameters);
		}

	};

	var ajaxSearchResultClick = function(data) {
		if (data.url) {
			document.location.href = data.url;
		}
	};

	this.checkKey = function(event) {
		if (event.keyCode == 13) {
			self.submitForm();
		}
	};

	this.submitForm = function(event) {
		if (event) {
			eventsManager.preventDefaultAction(event);
		}
		var targetUrl = self.formElement.getAttribute('action');
		if (inputElement.value) {
			targetUrl += 'phrase:' + encodeURIComponent(inputElement.value.replace('/', '%s%')) + '/';
		}
		document.location.href = targetUrl;
	};
	controller.addListener('DOMContentReady', init);
}
