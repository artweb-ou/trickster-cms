window.MobileHeaderComponent = function(componentElement) {
	var drawer;
	var basketElement;
	var basketBadgeElement;

	var init = function() {
		var element;
		if (element = componentElement.querySelector('.mobileheader_drawer')) {
			drawer = new MobileHeaderComponentDrawer(element);
		}

		if (element = componentElement.querySelector('.mobileheader_control_menu')) {
			element.addEventListener('click', menuControlClick);
			var drawerControls = componentElement.querySelectorAll('.mobileheader_control[data-drawersection]');
			for (var i = drawerControls.length; i--;) {
				drawerControls[i].addEventListener('click', drawerControlClick);
			}
		}

		element = componentElement.querySelector('.mobileheader_drawer_close_button');
		if (element) {
			element.addEventListener('click', closeClick);
		}
		if (basketElement = componentElement.querySelector('.mobileheader_control_cart')) {
			if (basketBadgeElement = basketElement.querySelector('.mobileheader_cart_badge')) {
				controller.addListener('startApplication', updateCartBadge);
				controller.addListener('shoppingBasketUpdated', updateCartBadge);
			}
		}
	};
	var updateCartBadge = function() {
		var products = window.shoppingBasketLogics.productsAmount;
		if (basketBadgeElement) {
			basketBadgeElement.innerHTML = products;
			basketBadgeElement.style.display = products ? 'block' : '';
		}
		if (basketElement) {
			if (products) {
				basketElement.classList.add('mobileheader_control_cart_active');
			} else {
				basketElement.classList.remove('mobileheader_control_cart_active');
			}
		}
	};
	var closeClick = function(event) {
		drawer.close();
	};
	var drawerControlClick = function(event) {
		var selected = this.getAttribute('data-drawersection');
		if (drawer.getCurrentSection() && drawer.getCurrentSection() === selected) {
			drawer.close();
		} else {
			drawer.open(this.getAttribute('data-drawersection'))
		}
	};
	var menuControlClick = function(event) {
		mobileLogics.toggleMenu();
	};
	init();
};

window.MobileHeaderComponentDrawer = function(componentElement) {
	var contentElements = {};
	var currentContentElement;
	var innerElement;
	var init = function() {
		var elements = componentElement.querySelectorAll('.mobileheader_drawer_content');
		innerElement = componentElement.querySelector('.mobileheader_drawer_inner');
		for (var i = elements.length; i--;) {
			var element = elements[i];
			contentElements[element.getAttribute('data-drawersection')] = element;
		}
	};
	this.close = function() {
		if (!currentContentElement) {
			return;
		}
		TweenLite.to(componentElement, 0.4, {
			'css': {'height': 0},
			'ease': Power2.easeInOut,
			'onComplete': function() {
				currentContentElement.style.display = 'none';
				currentContentElement = null;
			}
		});
	};
	this.getCurrentSection = function() {
		return currentContentElement
			? currentContentElement.getAttribute('data-drawersection')
			: '';
	};
	this.open = function(section) {
		if (currentContentElement) {
			currentContentElement.style.display = 'none';
		}
		currentContentElement = contentElements[section];
		if (currentContentElement) {
			currentContentElement.style.display = 'block';
		}
		TweenLite.to(componentElement, 0.4, {
			'css': {'height': innerElement.scrollHeight},
			'ease': Power2.easeInOut
		});
	};
	init();
};