window.importFormLogics = new function() {
	var records = [];

	var initLogics = function() {
		if (window.importFormRecords) {
			records = window.importFormRecords;
		}
	};
	var initComponents = function() {
		var elements = _('.importform_form');
		for (var i = 0; i < elements.length; i++) {
			new ImportFormComponent(elements[i]);
		}
	};
	this.getRecords = function() {
		return records;
	};
	controller.addListener('initLogics', initLogics);
	controller.addListener('initDom', initComponents);
};