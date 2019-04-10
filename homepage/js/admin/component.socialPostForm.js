window.SocialPostFormComponent = function(componentElement) {
	var self = this;

	var formElement;
	var linkTitleElement;
	var linkUrlElement;
	var linkDescriptionElement;
	var searchInputElement;
	var currentImageElement;
	var replacementImagePreviewElement;
	var replacementImageInputElement;
	var importInfoElement;
	var importInfoTitle;
	var importInfoButton;
	var imagePriviewContainer;
	var inputImage;

	var currentImportData;

	var init = function() {
		if (componentElement) {
			linkTitleElement = _('.socialpost_linktitle', formElement)[0];
			linkUrlElement = _('.socialpost_linkurl', formElement)[0];
			linkDescriptionElement = _('.socialpost_linkdescription', formElement)[0];
			currentImageElement = _('.socialpost_currentimage', formElement)[0];
			replacementImagePreviewElement = _('.socialpost_replacementimage_preview', formElement)[0];
			replacementImageInputElement = _('.socialpost_replacementimage', formElement)[0];
			imagePriviewContainer = _('.form_image_component' , formElement)[0];
			inputImage = _('.file_input_field', imagePriviewContainer.parentElement)[0];

			if (searchInputElement = _('.socialpost_search', formElement)[0]) {
				var types = searchInputElement.getAttribute('data-types');
				var apiMode = 'public';

				new AjaxSelectComponent(searchInputElement, types, apiMode, clickCallback);
			}

			importInfoElement = document.createElement('div');
			importInfoElement.className = 'socialpost_import_info';
			importInfoTitle = document.createElement('span');
			importInfoTitle.className = 'socialpost_import_title';
			importInfoButton = document.createElement('input');
			importInfoButton.className = 'socialpost_import button primary_button';
			importInfoButton.type = 'button';
			importInfoButton.value = "Import";
			searchInputElement.parentElement.appendChild(importInfoElement);
			importInfoElement.appendChild(importInfoButton);
			importInfoElement.appendChild(importInfoTitle);

			if (importInfoButton) {
				eventsManager.addHandler(importInfoButton, 'mouseup', importData);
			}
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
				linkTitleElement.value = currentImportData.title;
			}
			if (currentImportData.url) {
				linkUrlElement.value = currentImportData.url;
			}
			if (currentImportData.introduction) {
				if (CKEDITOR && CKEDITOR.instances[linkDescriptionElement.name]) {
					CKEDITOR.instances[linkDescriptionElement.name].setData(currentImportData.introduction);
				}
			}
			if (currentImportData.image) {
				currentImageElement.src = window.baseURL + 'image/type:/id:' + currentImportData.image;
				inputImage.innerHTML = currentImportData.image;
				if(imagePriviewContainer.classList.contains('form_image_component_hidden')) {
					imagePriviewContainer.classList.remove('form_image_component_hidden');
				}
			}
			importInfoElement.style.display = 'none';
		}
	};

	init();
};