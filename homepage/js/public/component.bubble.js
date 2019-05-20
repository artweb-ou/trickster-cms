window.BubbleComponent = function(referralElement, message, additionalClassName, bubbleCloseTag, waitDelay) {
	waitDelay = waitDelay?waitDelay:1000;

	var componentElement = false;
	var contentElement = false;
	var contentElementClassName;
	var headerElement = false;
	var middleElement = false;
	var footerElement = false;

	var positionedX = false;
	var positionedY = false;
	var bubbleWidth = false;
	var bubbleLeft;
	var bubbleTopStart;
	var bubbleTopStop;
	var positionedW;
	var positionedH;

	var bubbleHeight;
	var bubbleHeightDigit;


	var init = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'tip_popup';
		componentElement.style.display = 'none';

		contentElement = document.createElement('div');
		contentElementClassName = 'tip_popup_content ' + additionalClassName;
		contentElement.className = contentElementClassName;

		headerElement = document.createElement('div');
		headerElement.className = 'notice_header';
		middleElement = document.createElement('div');
		middleElement.className = 'notice_middle';
		footerElement = document.createElement('div');
		footerElement.className = 'notice_footer';

		if (message['title']){
			headerElement.innerHTML = '<span class="notice_title">'+message['title']+'</span>';
			contentElement.appendChild(headerElement);
		}
		if (message['content']){
			middleElement.innerHTML = message['content'];
			contentElement.appendChild(middleElement);
		}
		if (message['footer']){
			footerElement.innerHTML = message['footer'];
			contentElement.appendChild(footerElement);
		}


		componentElement.appendChild(contentElement);

		document.body.appendChild(componentElement);

	};
	this.start = function() {
		componentElement.style.display = 'block';

		var html = document.documentElement;
		var htmlScroll = html.scrollTop;
	//	var htmlWidth = document.body.clientWidth || document.documentElement.clientWidth || window.innerWidth;

		var parentPositions = referralElement.parentElement.getBoundingClientRect(); // span-checkbox

		positionedX = parentPositions.left;
		// positionedX = parentPositions.left;
		positionedW = parentPositions.width;
		positionedY = parentPositions.top;
		positionedH = parentPositions.height;

		var bubbleGetCompStyle = getComputedStyle(componentElement);
		bubbleHeight = bubbleGetCompStyle.height;
		bubbleHeightDigit = parseFloat(bubbleHeight);

		startY = positionedY + htmlScroll;
	//	componentElement.style.right = '-'+bubbleWidth;
	//	componentElement.style.transform = "translateX(-"+ startX + "px)";

		bubbleWidth 	= positionedW + 'px';
		bubbleLeft		= positionedX + 'px';
		bubbleTopStart 	= startY + 'px';
		bubbleTopStop 	= startY + positionedH - bubbleHeightDigit + 'px';
		componentElement.style.top = bubbleTopStop;
		componentElement.style.left = bubbleLeft;
		componentElement.style.width = bubbleWidth;
		componentElement.style.lineHeight = 0;
		componentElement.style.height = 0;
		componentElement.style.overflow = 'hidden';
		componentElement.style.opacity = 0;

		TweenLite.to(componentElement, 0.5, {'css': {'opacity': 1,'lineHeight': 'inherit','hight': 'auto',','minHeight': bubbleHeight,'overflow': 'visible','left': bubbleLeft, 'top': bubbleTopStop}, 'onComplete': wait});

		if (bubbleCloseTag){
			document.querySelector('.' + additionalClassName + ' .' + bubbleCloseTag).addEventListener('click', function(ev){
				ev.preventDefault();
				contentElement.parentNode.removeChild(contentElement);
			});
		}
	};
	var wait = function() {
		window.setTimeout(completeAnimation, waitDelay);
	};
	var completeAnimation = function() {
		TweenLite.to(componentElement, 0.5, {'css': {'opacity': 0, 'left': bubbleLeft, 'top': bubbleTopStop}, 'onComplete': destroyComponent});
	};
	var destroyComponent = function() {
		if(componentElement){
		componentElement.parentNode.removeChild(componentElement);
		}
	};

	init();
};