window.NewsMailInfoFormComponent = function(componentElement) {
	var self = this;

	var formElement;
	var contentElement;
	var searchInputElement;
	var importInfoElement;
	var importInfoTitle;
	var importInfoButton;

	var currentImportData;

	var init = function() {
		if (componentElement) {
			formElement = _('.newsmailinfo_form', componentElement)[0]
			contentElement = _('.newsmailinfo_content', componentElement)[0];
			importInfoElement = _('.newsmailinfo_import_info', componentElement)[0];
			importInfoTitle = _('.newsmailinfo_import_title', importInfoElement)[0];
			if (importInfoButton = _('.newsmailinfo_import', componentElement)[0]) {
				eventsManager.addHandler(importInfoButton, 'mouseup', importData);
			}
		}
		if (searchInputElement = _('.newsmailinfo_search', componentElement)[0]) {
			var types = searchInputElement.getAttribute('data-types');
			var apiMode = 'public';

			new AjaxSelectComponent(searchInputElement, types, apiMode, clickCallback);
		}
	};

	var clickCallback = function(data) {
		currentImportData = data;
		importInfoTitle.innerHTML = data.title;
		importInfoElement.style.display = 'block';
	};

	var importData = function() {
		if (currentImportData) {
			var content = '';
			if (currentImportData.title) {
				content += '<h2>' + currentImportData.title + '</h2>';
			}
			if (currentImportData.introduction) {
				content += currentImportData.introduction;
			}
			if (currentImportData.image) {
				content += '<div><img src="' + window.baseURL + 'image/type:socialImage/id:' + currentImportData.image + '/filename:' + currentImportData.image + '" /></div><br /><br />';
			}

			if (CKEDITOR && CKEDITOR.instances[contentElement.name]) {
				var data = CKEDITOR.instances[contentElement.name].getData();
				CKEDITOR.instances[contentElement.name].setData(data + '<br />' + content);
			}
			importInfoElement.style.display = 'none';
		}
	};

	init();
};