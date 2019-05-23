window.FeedbackFormComponent = function(componentElement) {
  var buttonText = '';
  var fileInputElement;
  var dropAreaElement;
  var controlElement;
  var dropArea;
  var button;

  var init = function() {
    new AjaxFormComponent(componentElement, tracking.feedbackTracking);

    if (fileInputElement = componentElement.querySelector('.fileinput_placeholder')) {
      dropAreaElement = componentElement;
      dropAreaElement.addEventListener('dragover', dropAreaDragEnterHandler, true);
      dropAreaElement.addEventListener('dragenter', dropAreaDragEnterHandler, true);
      dropAreaElement.addEventListener('dragleave', dropAreaDragLeaveHandler, true);
      dropAreaElement.addEventListener('drop', dropAreaDropHandler, true);
    }
  };
  var dropAreaDropHandler = function(event) {
    eventsManager.cancelBubbling(event);
    eventsManager.preventDefaultAction(event);
    hideDropArea();
    importFilesInfo(event.dataTransfer.files);
  };
  var dropAreaDragLeaveHandler = function(event) {
    eventsManager.preventDefaultAction(event);
    eventsManager.cancelBubbling(event);
    hideDropArea();
  };
  var hideDropArea = function() {
    componentElement.classList.remove('feedback_dragged');
  };
  var showDropArea = function() {
    componentElement.classList.add('feedback_dragged');
  };
  var dropAreaDragEnterHandler = function(event) {
    eventsManager.preventDefaultAction(event);
    eventsManager.cancelBubbling(event);
    showDropArea();
  };
  var importFilesInfo = function(files) {
    fileInputElement.files = files;
    eventsManager.fireEvent(fileInputElement, 'change');
  };
  // var createDom = function() {
  // 	var container = document.createElement('div');
  // 	var button = document.createElement('div');
  // 	var span = document.createElement('div');
  // 	span.className = 'feedback_attach_file_text';
  // 	span.innerText = 'Attach file';
  // 	button.className = 'feedback_attach_file';
  // 	container.className = 'feedback_attach_file_container';
  // 	container.appendChild(button);
  // 	container.appendChild(span);
  // 	controlElement.insertBefore(container, controlElement.firstChild);
  // 	createDropArea();
  // };
  // var createDropArea = function() {
  // 	dropArea = document.createElement('div');
  // 	dropArea.className = 'feedback_form_droparea';
  // 	dropArea.style.visibility = 'hidden';
  // 	dropArea.style.position = 'absolute';
  // 	dropArea.style.top = 0;
  // 	dropArea.style.bottom = 0;
  // 	dropArea.style.left = 0;
  // 	dropArea.style.right = 0;
  // 	var logo = document.createElement('div');
  // 	logo.className = 'droparea_logo';
  // 	var span = document.createElement('span');
  // 	span.innerText = 'Drop your file to attach';
  // 	dropArea.appendChild(logo);
  // 	dropArea.appendChild(span);
  // 	dropAreaElement.parentElement.appendChild(dropArea);
  // 	dropArea.ondrop = function(event) {
  // 		dropAreaDropHandler(event);
  // 	}
  // };
  // var processInputElement = function() {
  // 	inputElement.style.position = 'absolute';
  // 	inputElement.style.visibility = 'hidden';
  // 	inputElement.style.left = 0;
  // 	inputElement.style.top = 0;
  // };
  // var clickHandler = function() {
  // 	inputElement.click();
  // };

  init();
};