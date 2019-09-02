window.templatesManager = new function() {
	var templates = {};

	var init = function() {
		if (typeof window.templates !== 'undefined') {
			templates = window.templates;
		}
	};
	this.get = function(name) {
		return templates[name] || '';
	};
	controller.addListener('initLogics', init);
};