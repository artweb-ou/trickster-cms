window.BubbleComponent = function(referralElement, content) {
	var waitDelay = 700;

	var componentElement = false;
	var backgroundElement = false;
	var contentElement = false;

	var positionedX = false;
	var positionedY = false;
	var yOffset = 16;

	var init = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'tip_popup';
		componentElement.style.display = 'none';

		contentElement = document.createElement('div');
		contentElement.className = 'tip_popup_content';
		contentElement.innerHTML = content;
		componentElement.appendChild(contentElement);

		document.body.appendChild(componentElement);

	};
	this.start = function() {
		var parentPositions = window.mouseTracker.getElementCoordinates(referralElement);

		componentElement.style.display = 'block';

		positionedX = parentPositions.left + referralElement.offsetWidth / 2 - componentElement.offsetWidth / 2;
		positionedY = parentPositions.top - componentElement.offsetHeight - yOffset / 2;

		var startX = positionedX;
		var startY = positionedY + yOffset;
		componentElement.style.left = startX + 'px';
		componentElement.style.top = startY + 'px';

		opacityHandler.setOpacity(backgroundElement, 0);
		TweenLite.to(componentElement, 0.4, {'css': {'opacity': 1, 'left': positionedX, 'top': positionedY}, 'onComplete': wait});
	};
	var wait = function() {
		window.setTimeout(completeAnimation, waitDelay);
	};
	var completeAnimation = function() {
		var endY = positionedY - yOffset;
		TweenLite.to(componentElement, 0.5, {'css': {'opacity': 0, 'left': positionedX, 'top': endY}, 'onComplete': destroyComponent});
	};
	var destroyComponent = function() {
		componentElement.parentNode.removeChild(componentElement);
	};

	init();
};