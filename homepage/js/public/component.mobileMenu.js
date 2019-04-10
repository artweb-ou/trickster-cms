window.MobileMenuComponent = function(componentElement) {
	var self = this;
	var closeIconElement;
	var visible;

	var init = function() {
		closeIconElement = componentElement.querySelector('.mobilemenu_closeicon');
		var element = componentElement.querySelector('.mobilemenu_main');
		element.addEventListener('click', mainClick);
		componentElement.addEventListener('click', click);
		closeIconElement.addEventListener('click', closeClick);
	};
	this.toggleVisibility = function() {
		visible = !visible;
		if (visible) {
			DarkLayerComponent.showLayer(function() {
				self.toggleVisibility
			}, null);
			domHelper.addClass(componentElement, 'mobilemenu_visible');
			TweenLite.to(componentElement, 0.3, {
				'css': {'right': 0},
				'ease': Power2.easeInOut,
				'onComplete': function() {
				}
			});
		} else {
			TweenLite.to(componentElement, 0.3, {
				'css': {'right': '100%'},
				'ease': Power2.easeInOut,
				'onComplete': function() {
					domHelper.removeClass(componentElement, 'mobilemenu_visible');
				}
			});
			DarkLayerComponent.hideLayer();
		}
	};
	var click = function(event) {
		self.toggleVisibility();
	};
	var mainClick = function(event) {
		event.stopPropagation();
	};
	var closeClick = function(event) {
		self.toggleVisibility();
	};
	init();
};
DomHelperMixin.call(MobileMenuComponent.prototype);