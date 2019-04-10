window.NewsMailTextSubContentFormComponent = function(componentElement) {
	var self = this;
	var titleElement, contentElement, linkElement;
	var imagePriviewContainer
	var currentImageElement;
	var searchInputElement;
	var importInfoElement;
	var importInfoTitle;
	var importInfoButton;
	var typeElement;
	var replacementImage;

	var currentImportData;

	var init = function() {
		titleElement = _('.newsmailtextsubcontent_import_infotitle', componentElement)[0];
		contentElement = _('.newsmailtextsubcontent_import_content_info', componentElement)[0];
		linkElement = _('.newsmailtextsubcontent_import_link', componentElement)[0];
		typeElement = _('.newsmailtextsubcontent_import_structure_type', componentElement)[0];
		imagePriviewContainer = _('.form_image_component', componentElement)[0]

		importInfoElement = _('.newsmailtextsubcontent_import_info', componentElement)[0];
		importInfoTitle = _('.newsmailtextsubcontent_import_title', componentElement)[0];
		replacementImage = _('.replacementImage', componentElement)[0];
		currentImageElement = _('.newsmailtextsubcontent_form_currentimage', componentElement)[0];
		if (importInfoButton = _('.newsmailtextsubcontent_import', componentElement)[0]) {
			eventsManager.addHandler(importInfoButton, 'mouseup', importData);
		}
		if (searchInputElement = _('.newsmailtextsubcontent_form_search', componentElement)[0]) {

			var types = searchInputElement.getAttribute('data-types');
			var apiMode = 'public';

			var searchInput = new AjaxSelectComponent(searchInputElement, types, apiMode, clickCallback);
			searchInput.removeAllOptions();
		}
	};

	var clickCallback = function(data) {
		currentImportData = data;
		importInfoTitle.innerHTML = data.title;
		importInfoElement.style.display = 'block';
	};

	var importData = function() {
		if (currentImportData) {
			if (currentImportData.title) {
				titleElement.value = currentImportData.title;
			}

			if (currentImportData.structureType) {
				typeElement.value = currentImportData.structureType;
			}
			if (currentImportData.introduction) {
				if (CKEDITOR && CKEDITOR.instances[contentElement.name]) {
					CKEDITOR.instances[contentElement.name].setData(currentImportData.introduction);
				}
			}
			else if (currentImportData.content) {
				if (CKEDITOR && CKEDITOR.instances[contentElement.name]) {
					CKEDITOR.instances[contentElement.name].setData(currentImportData.content);
				}
			}
			if (currentImportData.shopUrl != undefined && currentImportData.shopUrl) {
				linkElement.value = currentImportData.shopUrl;
			}
			else if (currentImportData.url) {
				linkElement.value = currentImportData.url;
			}

			if (currentImportData.image) {
				replacementImage.value = currentImportData.image;
				currentImageElement.src = window.baseURL + 'image/type:/id:' + currentImportData.image;
				if(imagePriviewContainer.classList.contains('form_image_component_hidden')) {
					imagePriviewContainer.classList.remove('form_image_component_hidden');
				}
			}

			importInfoElement.style.display = 'none';
		}
	};

	init();
};