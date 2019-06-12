window.FeedbackDragAndDropComponent = function(componentElement) {
	var fakeField;
	var fakeButton;
	var buttonText = '';
	var inputElement;
	var dropAreaElement;
	var controlElement;
	var dropArea;
	var button;

	var init = function() {
		inputElement = componentElement.querySelector('.feedback_fileinput_placeholder');
		controlElement = componentElement.querySelector('.feedback_controls');
		dropAreaElement = componentElement.querySelector('.textarea_component');
		createDom();

		button = componentElement.querySelector('.feedback_attach_file_container');

		processInputElement();
		if (window.translationsLogics.get('button.file_upload')) {
			buttonText = window.translationsLogics.get('button.file_upload');
		}
		if (inputElement) {
			eventsManager.addHandler(dropAreaElement, 'dragenter', dropAreaDragEnterHandler, false);
			eventsManager.addHandler(dropArea, 'dragover', dropAreaDragEnterHandler, false);
			eventsManager.addHandler(dropArea, 'dragleave', dropAreaDragLeaveHandler, false);
			eventsManager.addHandler(button, 'click', clickHandler);
		}
	};
	var dropAreaDropHandler = function(event) {
		importFilesInfo(event.dataTransfer.files);
		eventsManager.cancelBubbling(event);
		eventsManager.preventDefaultAction(event);

	};
	var dropAreaDragLeaveHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		hideDropArea();
	};
	var hideDropArea = function() {
		dropAreaElement.style.visibility = 'visible';
		dropArea.style.visibility = 'hidden';
	};
	var showDropArea = function() {
		dropArea.style.visibility = 'visible';
		dropAreaElement.style.visibility = 'hidden';
	};
	var dropAreaDragEnterHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		eventsManager.cancelBubbling(event);
		showDropArea();

	};
	var createDom = function() {
		var container = document.createElement('div');
		var button = document.createElement('div');
		var span = document.createElement('div');
		span.className = 'feedback_attach_file_text';
		span.innerText = 'Attach file';
		button.className = 'feedback_attach_file';
		container.className = 'feedback_attach_file_container';
		container.appendChild(button);
		container.appendChild(span);
		controlElement.insertBefore(container, controlElement.firstChild);
		createDropArea();
	};
	var createDropArea = function() {
		dropArea = document.createElement('div');
		dropArea.className = 'feedback_form_droparea';
		dropArea.style.visibility = 'hidden';
		dropArea.style.position = 'absolute';
		dropArea.style.top = 0;
		dropArea.style.bottom = 0;
		dropArea.style.left = 0;
		dropArea.style.right = 0;
		var logo = document.createElement('div');
		logo.className = 'droparea_logo';
		var span = document.createElement('span');
		span.innerText = 'Drop your file to attach';
		dropArea.appendChild(logo);
		dropArea.appendChild(span);
		dropAreaElement.parentElement.appendChild(dropArea);
		dropArea.ondrop = function(event) {
			dropAreaDropHandler(event);
		}
	};
	var processInputElement = function() {
		inputElement.style.position = 'absolute';
		inputElement.style.visibility = 'hidden';
		inputElement.style.left = 0;
		inputElement.style.top = 0;
	};
	var clickHandler = function() {
		inputElement.click();
	};
	var importFilesInfo = function(files) {
		inputElement.files = files;
		hideDropArea();
	};
	init();
};