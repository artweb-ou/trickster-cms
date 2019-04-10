window.subscriptionLogics = new function() {
	var initComponents = function() {
		var elements = _('.news_mailform_form');
		for (var i = 0; i < elements.length; i++) {
			new SubscriptionFormComponent(elements[i]);
		}
	}
	controller.addListener('initDom', initComponents);
}