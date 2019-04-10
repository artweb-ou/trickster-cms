window.campaignsListFormLogics = new function() {
	var initComponents = function() {
		var elements = _('.campaignslist');
		for (var i = elements.length; i--;) {
			new CampaignsListFormComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};