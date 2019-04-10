window.selectedCampaignsLogics = new function() {
	var initComponents = function() {
		var elements = _('.selectedcampaigns_form');
		for (var i = elements.length; i--;) {
			new SelectedCampaignsComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
}