window.OrderProductFormComponent = function(componentElement) {
	var self = this;
	var title;
	var box;
	var productIdElement;
	var removeButton;
	var searchElement;


	var init = function() {
		box = _('.order_product_box', componentElement)[0];
		title = _('.order_product_title', componentElement)[0];
		productIdElement = _('.product_id', componentElement)[0];
		if (removeButton = _(".order_product_remover", componentElement)[0]) {
			eventsManager.addHandler(removeButton, 'click', removeResult);
		}
		if (searchElement = _('.product_search', componentElement)[0]) {
			var types = searchElement.getAttribute('data-types');
			var apiMode = "admin";
			new AjaxSelectComponent(searchElement, types, apiMode, ajaxSearchCallback);
		}
	};

	var ajaxSearchCallback = function(response) {
		title.innerHTML = response.title;
		productIdElement.value = response.id;
		box.style.display = "block";
		searchElement.value = "";
		searchElement.blur();
	};

	var removeResult = function() {
		removeButton.style.display = "none";
		productIdElement.value = "";
		title.innerHTML = "";
	};

	init();
};