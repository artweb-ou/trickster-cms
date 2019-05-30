window.GalleryFormComponent = function(componentElement) {
	var uploadForm;
	var filesInput;
	var submitButton;
	var dropAreaElement;
	var imagesListElement;

	var uploadingImagesLimit = 1;

	var actionURL;
	var elementId;
	var linkType;
	var actionName;
	var fieldName;

	var uploadFileComponents = [];

	var init = function() {
		imagesListElement = _('.gallery_form_images_list', componentElement)[0];

		var multipleUploadSupported = false;

		if (uploadForm = _('.gallery_form_upload', componentElement)[0]) {
			actionURL = uploadForm.getAttribute('action').replace('/admin/', '/adminAjax/');
			var actionInput = _('.gallery_form_upload_action_input', uploadForm)[0];
			if (actionInput) {
				actionName = actionInput.value;
			}
			var elementIdInput = _('.gallery_form_upload_elementid_input', uploadForm)[0];
			if (elementIdInput) {
				elementId = elementIdInput.value;
			}
			var linkTypeInput = _('.gallery_form_upload_linktype', uploadForm)[0];
			if (linkTypeInput) {
				linkType = linkTypeInput.value;
			}
			if (filesInput = _('.gallery_form_upload_input', uploadForm)[0]) {
				fieldName = filesInput.name;
				filesInput.multiple = true;
				filesInput.value = '';
				if (typeof filesInput.files !== 'undefined') {
					multipleUploadSupported = true;
				}
			}
		}
		if (submitButton = _('.gallery_form_upload_submit', componentElement)[0]) {
			if (multipleUploadSupported) {
				eventsManager.addHandler(filesInput, 'change', filesInputChangeHandler);
				if (uploadForm = _('.gallery_form_upload', componentElement)[0]) {
					eventsManager.addHandler(submitButton, 'click', submitButtonClickHandler);
				}
				if (dropAreaElement = _('.gallery_form_upload_droparea', componentElement)[0]) {
					dropAreaElement.style.display = 'block';
					eventsManager.addHandler(dropAreaElement, 'dragenter', dropAreaDragEnterHandler, true);
					eventsManager.addHandler(dropAreaElement, 'dragover', dropAreaDragEnterHandler, true);
					eventsManager.addHandler(dropAreaElement, 'dragleave', dropAreaDragLeaveHandler, true);
					eventsManager.addHandler(dropAreaElement, 'drop', dropAreaDropHandler, true);
				}

				refreshContents();
			} else {
				submitButton.style.display = 'block';
			}
		}
	};
	var refreshContents = function() {
		if (uploadFileComponents.length > 0) {
			submitButton.style.display = 'block';
		} else {
			submitButton.style.display = 'none';
		}
	};

	var dropAreaDragEnterHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		dropAreaElement.className = 'gallery_form_upload_droparea gallery_form_upload_droparea_hover';
	};
	var dropAreaDragLeaveHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		dropAreaElement.className = 'gallery_form_upload_droparea';
	};
	/**
	 * @param {DragEvent} event
	 */
	var dropAreaDropHandler = function(event) {
		eventsManager.cancelBubbling(event);
		eventsManager.preventDefaultAction(event);
		importFilesInfo(event.dataTransfer.files);
		refreshContents();
	};
	var submitButtonClickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		checkUpload();
	};
	var filesInputChangeHandler = function() {
		importFilesInfo(filesInput.files);
		refreshContents();
	};
	var importFilesInfo = function(files) {
		var i;
		for (i = 0; i < uploadFileComponents.length; i++) {
			imagesListElement.removeChild(uploadFileComponents[i].componentElement);
		}

		uploadFileComponents = [];
		for (i = 0; i < files.length; i++) {
			if (files[i].size <= 1024 * 1024 * 200 && (files[i].type.search(/image\/.*/) != -1 || files[i].type.search(/video\/mp4/) != -1)) {
				var fileComponent = new GalleryFormImageComponent(files[i]);
				imagesListElement.appendChild(fileComponent.componentElement);

				uploadFileComponents.push(fileComponent);
			}
		}
	};
	var checkUpload = function() {
		if (uploadFileComponents.length > 0) {
			var uploadingImages = 0;
			for (var i = 0; i < uploadFileComponents.length; i++) {
				if (uploadFileComponents[i].status == 'awaiting') {
					uploadFileComponents[i].startUpload(actionURL, elementId, actionName, fieldName, linkType, checkUpload);
					uploadingImages++;
				}
				if (uploadingImages >= uploadingImagesLimit) {
					break;
				}
			}
		}
	};

	init();
};
window.GalleryFormImageComponent = function(fileInfo) {

	var self = this;

	this.componentElement = null;
	this.status = false;

	var galleryCallBack;

	var imageCell;
	var imageElement;
	var titleCell;
	var altCell;
	var statusCell;
	var statusElement;
	var editCell;
	var deleteCell;

	var progress = 0;

	var init = function() {
		self.componentElement = document.createElement("tr");
		var checkboxCell = document.createElement('td');
		var checkbox = document.createElement('span');
		checkbox.className = 'checkbox singlebox';
		checkboxCell.appendChild(checkbox);
		self.componentElement.appendChild(checkboxCell);
		imageCell = document.createElement('td');
		imageCell.className = 'gallery_form_image_imagecell';
		self.componentElement.appendChild(imageCell);
		titleCell = document.createElement('td');
		titleCell.className = 'generic gallery_form_image_title';
		self.componentElement.appendChild(titleCell);
		altCell = document.createElement('td');
		altCell.className = 'generic gallery_form_image_alt';
		self.componentElement.appendChild(altCell);
		statusCell = document.createElement('td');
		statusCell.className = 'generic gallery_form_image_status';
		statusCell.setAttribute('colspan',3);
		self.componentElement.appendChild(statusCell);
		editCell = document.createElement('td');
		editCell.setAttribute('style','display:none');
		self.componentElement.appendChild(editCell);
		deleteCell = document.createElement('td');
		deleteCell.setAttribute('style','display:none');
		self.componentElement.appendChild(deleteCell);

		fillUploadInfo();
		refreshContents();
	};
	var fillUploadInfo = function() {
		imageElement = document.createElement('img');
		imageElement.className = 'gallery_form_image_image';

		if (typeof FileReader !== 'undefined') {
			var reader = new FileReader();
			reader.onload = updateImageThumbnail;
			reader.readAsDataURL(fileInfo);
		}
		imageCell.appendChild(imageElement);

		altCell.innerHTML = fileInfo.name;

		statusElement = document.createElement('div');
		statusCell.appendChild(statusElement);

		self.status = 'awaiting';
	};
	var updateImageThumbnail = function(event) {
		imageElement.src = event.target.result;
	};
	this.startUpload = function(actionURL, id, action, fieldName, linkType, callBack) {
		self.status = 'inprogress';
		refreshContents();

		galleryCallBack = callBack;

		var parameters = {};
		parameters.requestXML = false;
		parameters.requestURL = actionURL;
		parameters.requestType = 'POST';
		parameters.contentType = 'multipart/form-data';
		parameters.postParameters = {
			'id': id,
			'action': action
		};
		if (linkType) {
			parameters.postParameters.linkType = linkType;
		}
		parameters.postParameters[fieldName] = fileInfo;
		parameters.progressCallBack = progressChangeHandler;
		parameters.successCallBack = uploadSuccessHandler;
		parameters.failCallBack = uploadFailHandler;
		parameters.failureDelay = false;
		window.ajaxManager.makeRequest(parameters);
	};
	/**
	 * @param {ProgressEvent} event
	 */
	var progressChangeHandler = function(event) {
		if (event.lengthComputable) {
			progress = (event.loaded / event.total) * 100;
		}
		refreshContents()
	};
	var uploadSuccessHandler = function() {
		self.status = 'uploaded';
		refreshContents();
		if (galleryCallBack) {
			galleryCallBack();
		}
	};
	var uploadFailHandler = function() {
		self.status = 'failed';
		refreshContents();
		if (galleryCallBack) {
			galleryCallBack();
		}
	};
	var refreshContents = function() {
		var statusText = '';
		if (self.status == 'failed') {
			statusText = translationsLogics.get('gallery.failed'); //'Üleslaadimine ebaõnnestus';
		} else if (self.status == 'uploaded') {
			statusText = translationsLogics.get('gallery.uploaded'); // 'Laetud ülesse';
		} else if (self.status == 'inprogress') {
			statusText = translationsLogics.get('gallery.inprogress') + ' (' + progress.toFixed(2) + "%)"; //'Laetakse ülesse (' + progress.toFixed(2) + "%)";
		} else if (self.status == 'awaiting') {
			statusText = translationsLogics.get('gallery.awaiting'); //'Ootab üleslaadimist';
		}
		statusElement.innerHTML = statusText;
	};

	init();
};