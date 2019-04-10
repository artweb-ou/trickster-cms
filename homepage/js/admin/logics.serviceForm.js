window.serviceFormLogics = new function() {
	var init = function() {
		var gallerySelector;
		if (gallerySelector = _('.service_form_galleryselector')[0]) {
			new AjaxSelectComponent(gallerySelector, 'gallery', 'admin');
		}
	};
	controller.addListener('initDom', init);
}