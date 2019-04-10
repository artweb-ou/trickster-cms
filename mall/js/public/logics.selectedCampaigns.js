window.selectedCampaignsLogics = new function() {
	var initComponents = function() {
		var elements = _('.scrollitems');
		for (var i = elements.length; i--;) {
			new SelectedCampaignsScrollComponent(elements[i]);
		}
	};
	controller.addListener('DOMContentReady', initComponents);
};