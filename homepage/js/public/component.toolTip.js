window.ToolTipComponent = function(parameters, popupText_deprecated, excludedElements_deprecated, classNameExtra_deprecated) {
	var self = this;
	var referralElement;
	var componentElement;
	var contentElement;
	var popupText;
	var popupOffset = 12;
	var displayDelay = 100;
	var displayed = false;
	var displayAllowed;
	var displayTimeout;
	var hideOnClick;
	var hideOnLeave;
	var fixedX = false;
	var fixedY = false;
	var excludedElements;
	var classNameExtra;
	var beforeDisplay;
	var behaviourType = 'mouseover';
	var elementsCreated = false;
	var attached = false;
	var init = function() {
		//backward compatibility with old arguments, comes first
		if (typeof parameters == 'object') {
			parseParameters(parameters);
		} else {
			referralElement = parameters;
		}
		if (popupText_deprecated) {
			popupText = popupText_deprecated;
		}
		if (excludedElements_deprecated) {
			excludedElements = excludedElements_deprecated;
		}
		if (classNameExtra_deprecated) {
			classNameExtra = classNameExtra_deprecated;
		}

		addMouseHandlers();
	};
	var parseParameters = function(parameters) {
		if (typeof parameters.referralElement !== 'undefined') {
			referralElement = parameters.referralElement;
		}
		if (typeof parameters.popupText !== 'undefined') {
			popupText = parameters.popupText;
		}
		if (typeof parameters.classNameExtra !== 'undefined') {
			classNameExtra = parameters.classNameExtra;
		}
		if (typeof parameters.excludedElements !== 'undefined') {
			excludedElements = parameters.excludedElements;
		}
		if (typeof parameters.excludedElements !== 'undefined') {
			excludedElements = parameters.excludedElements;
		}
		if (typeof parameters.behaviourType !== 'undefined') {
			behaviourType = parameters.behaviourType;
		} else {
			behaviourType = 'mouseover';
		}
		if (typeof parameters.hideOnClick !== 'undefined') {
			hideOnClick = parameters.hideOnClick;
		} else {
			hideOnClick = true;
		}
		if (typeof parameters.beforeDisplay !== 'undefined') {
			beforeDisplay = parameters.beforeDisplay;
		} else {
			beforeDisplay = true;
		}
		if (typeof parameters.hideOnLeave !== 'undefined') {
			hideOnLeave = parameters.hideOnLeave;
		} else {
			hideOnLeave = true;
		}
	};
	var createDomElements = function() {
		elementsCreated = true;
		componentElement = document.createElement('div');
		componentElement.className = 'tip_popup';
		componentElement.style['pointerEvents'] = 'none';
		if (classNameExtra) {
			componentElement.className += ' ' + classNameExtra;
		}
		componentElement.style.display = 'none';

		contentElement = document.createElement('div');
		contentElement.className = 'tip_popup_content';
		componentElement.appendChild(contentElement);

		contentElement.innerHTML = popupText;
	};
	var attach = function() {
		if (!attached) {
			if (!elementsCreated){
				createDomElements();
			}
			attached = true;
			document.body.appendChild(componentElement);
		}
	};
	var detach = function() {
		if (attached) {
			attached = false;
			document.body.removeChild(componentElement);
		}
	};
	var moveHandler = function() {
		updatePosition();
	};
	var resizeHandler = function() {
		if (behaviourType == 'mouseover') {
			// handle zoom changes
			updatePosition();
		}
	};
	var enterHandler = function() {
		if (popupText && displayAllowed && !displayed) {
			displayTimeout = window.setTimeout(self.displayComponent, displayDelay);
		}
	};
	var overHandler = function(event) {
		displayAllowed = checkExcluded(event.target)
		if (beforeDisplay) {
			displayAllowed &= beforeDisplay();
		}
		if (popupText && (displayAllowed)) {
			if (!displayed) {
				self.displayComponent();
			}
		} else if (displayed) {
			self.hideComponent();
		}
	};
	var checkExcluded = function(element) {
		var result = true;
		if (excludedElements) {
			for (var i = 0; i < excludedElements.length; i++) {
				if ((excludedElements[i] === element) || isAChildOf(excludedElements[i], element)) {
					result = false;
					break;
				}
			}
		}
		return result;
	};
	var leaveHandler = function() {
		window.clearTimeout(displayTimeout);
		TweenLite.to(componentElement, 0.5, {
			'css': {'opacity': 0},
			'onComplete': self.hideComponent
		});
	};
	this.displayComponent = function() {
		if (!displayed) {
			attach();

			displayed = true;

			componentElement.style.opacity = 0;
			componentElement.style.display = 'block';

			updatePosition();

			TweenLite.to(componentElement, 0.5, {'css': {'opacity': 1}});
		}
	};
	this.hideComponent = function(callBack) {
		displayed = false;
		// componentElement.style.display = 'none';
		detach();
		if (callBack) {
			callBack();
		}
	};
	var updatePosition = function(e) {
		if (!displayed) {
			return;
		}
		var verticalMouseCoord = window.mouseTracker.mouseX;
		var verticalOffsetWidth = window.innerWidth;
		var popupHeight = componentElement.offsetHeight;
		var popupWidth = componentElement.offsetWidth;
		var xPosition = 0;
		if (fixedX) {
			xPosition = fixedX;
		} else {
			xPosition = window.mouseTracker.mouseX + popupOffset;
			if (verticalOffsetWidth - verticalMouseCoord < popupWidth) {
				xPosition = xPosition - popupWidth;
			}
		}
		var yPosition = 0;
		if (fixedY) {
			yPosition = fixedY - popupHeight;
		} else {
			yPosition = window.mouseTracker.mouseY - popupHeight - popupOffset;
		}


		componentElement.style.left = xPosition + 'px';
		componentElement.style.top = yPosition + 'px';
	};
	var isAChildOf = function(_parent, _child) {
		if (_parent === _child) {
			return false;
		}
		while (_child && _child !== _parent) {
			_child = _child.parentNode;
		}

		return _child === _parent;
	};
	var addMouseHandlers = function() {
		if (behaviourType == 'mouseover') {
			window.eventsManager.addHandler(window, 'resize', resizeHandler);
			window.eventsManager.addHandler(referralElement, 'mousemove', moveHandler);
			window.eventsManager.addHandler(referralElement, 'mouseover', overHandler);
			window.eventsManager.addHandler(referralElement, 'mouseenter', enterHandler);
			if (hideOnLeave) {
				window.eventsManager.addHandler(referralElement, 'mouseleave', leaveHandler);
			}
			if (hideOnClick) {
				window.eventsManager.addHandler(referralElement, 'click', leaveHandler);
			}
		}
	};
	this.setDisplayDelay = function(delay) {
		displayDelay = delay;
	};
	this.setText = function(text) {
		popupText = text;
		contentElement.innerHTML = popupText;
		updatePosition();
	};
	this.setFixedCoordinates = function(x, y) {
		fixedX = x;
		fixedY = y;
		updatePosition();
	};
	this.changeBehaviour = function(newType) {
		window.eventsManager.removeHandler(window, 'resize', resizeHandler);
		if (referralElement) {
			window.eventsManager.removeHandler(referralElement, 'mousemove', moveHandler);
			window.eventsManager.removeHandler(referralElement, 'mouseover', overHandler);
			window.eventsManager.removeHandler(referralElement, 'mouseenter', enterHandler)
			window.eventsManager.removeHandler(referralElement, 'mouseleave', leaveHandler);
			window.eventsManager.removeHandler(referralElement, 'click', leaveHandler);
		}
		behaviourType = newType;
		addMouseHandlers();
	};
	init();
};