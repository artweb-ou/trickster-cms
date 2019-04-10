window.SalesStatisticsFilterComponent = function(componentElement) {
	var datePresets;
	var init = function() {
		var categoryFilterBlock = _('.sales_statistics_filter_category_block', componentElement)[0];
		if(categoryFilterBlock) {
			new AjaxSelectComponent(categoryFilterBlock, "category", "admin");
		}

		var productFilterBlock = _('.sales_statistics_filter_product_block', componentElement)[0];
		if(productFilterBlock) {
			new AjaxSelectComponent(productFilterBlock, "product", "admin");
		}

		var userGroupFilterBlock = _('.sales_statistics_filter_user_group_block', componentElement)[0];
		if(userGroupFilterBlock) {
			new AjaxSelectComponent(userGroupFilterBlock, "userGroup", "admin");
		}
	};

	var startInput = _('.sales_statistics_filter_start', componentElement)[0];
	var endInput = _('.sales_statistics_filter_end', componentElement)[0];
	ActiveStatisticDateRange();
	datePresets = _('.sales_statistics_date_presets a', componentElement);
	for (var i = 0; i < datePresets.length; i++) {
		new SalesStatisticsDatePresetComponent(datePresets[i], startInput, endInput);
	}

	init();
};

window.SalesStatisticsDatePresetComponent = function(linkElement, startInput, endInput) {
	var start;
	var end;
	var init = function() {
		start = linkElement.dataset.start;
		end = linkElement.dataset.end;
		linkElement.classList.remove('sales_statistics_date_active');
		if(start === startInput.value && end === endInput.value) {
			linkElement.classList.add('sales_statistics_date_active');
		}
		eventsManager.addHandler(linkElement, 'click', clickHandler);
	};

	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		startInput.value = start;
		endInput.value = end;
	};

	init();
};

window.ActiveStatisticDateRange = function() {
	var components = document.querySelectorAll('.sales_statistics_date');
	var init = function() {
		for(var i = 0; i < components.length; i++) {
			eventsManager.addHandler(components[i], 'click', function(e) {clickHandler(e)});
		}
	};
	var clickHandler = function(e) {
		var component = document.querySelectorAll('.sales_statistics_date');
		for(var i = 0; i < component.length; i++) {
			if(component[i].classList.contains('sales_statistics_date_active')){
				component[i].classList.remove('sales_statistics_date_active');
			}
		}
		var element = e.target;
		element.classList.add('sales_statistics_date_active');
	};
	init();
};