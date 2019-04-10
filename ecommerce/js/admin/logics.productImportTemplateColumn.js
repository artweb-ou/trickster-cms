window.productImportTemplateColumnLogics = new function() {
	var initComponents = function() {
		var element = _('.productimporttemplatecolumn_form')[0];
		if (element) {
			new ProductImportTemplateColumnComponent(element);
		}
	};
	controller.addListener('initDom', initComponents);
};