window.buttonLogics = new function() {
	var init = function() {
		var elements = document.querySelectorAll('button.button[type=button]');
		for(var i = 0; i < elements.length; i++) {
			new ButtonComponent(elements[i]);
		}
	};
	controller.addListener('initDom',init);
};