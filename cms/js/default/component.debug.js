window.debugComponent = new function() {
	var debugInfoUpdate = function() {
		refreshContents();
	};
	var refreshContents = function() {
		if (!componentElement) {
			checkDom();
		}
		if (componentElement) {
			componentElement.style.display = 'block';
			var newRecord = window.debugLogics.getLastRecord();

			var recordRow = document.createElement('pre');
			recordRow.className = 'debug_block_row';
			recordRow.innerHTML = newRecord.text;
			if (componentElement.firstChild) {
				componentElement.insertBefore(recordRow, componentElement.firstChild);
			} else {
				componentElement.appendChild(recordRow);
			}
		}
	};
	var prepareDom = function() {
		componentElement = _('.debug_block')[0];
		if (componentElement) {
			eventsManager.addHandler(componentElement, 'click', clickHandler);
		}
	};
	var checkDom = function() {
		if (!componentElement && document.body) {
			componentElement = document.createElement('div');
			componentElement.className = 'debug_block';
			document.body.appendChild(componentElement);

			eventsManager.addHandler(componentElement, 'click', clickHandler);
		}
	};
	var clickHandler = function() {
		componentElement.style.display = 'none';
	};
	var self = this;
	var componentElement = false;

	controller.addListener('debugInfoUpdate', debugInfoUpdate);
	controller.addListener('initDom', prepareDom);
};