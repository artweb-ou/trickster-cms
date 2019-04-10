window.CarouselPagesMixin = function() {
	this.cpm_rotateInterval = null;
	this.cpm_rotateDelay = 1000;

	this.cpm_componentElement = null;
	this.cpm_originalPageElements = null;
	this.cpm_containerElement = null;
	this.cpm_leftElement = null;
	this.cpm_rightElement = null;
	this.cpm_contentElement = null;
	this.cpm_centerElement = null;
	this.cpm_onScrollFinishCallback = null;
	this.cpm_leftPageElements = null;
	this.cpm_rightPageElements = null;
	this.cpm_rotateSpeed = 1.5;
	this.cpm_currentNumber = null;
	this.cpm_imageAspectRatio = 1;
	this.cpm_autoStart = true;
	this.cpm_scope = this;

	this.cpm_preloadCallBack = false;
	this.cpm_preloadedImagesIndex = {};

	this.initCarouselGallery = function(options) {
		var scope = this;
		this.cpm_parseOptions(options);
		if (this.cpm_componentElement) {
			if (this.cpm_originalPageElements.length > 0) {
				if (!this.cpm_containerElement) {
					this.cpm_containerElement = this.cpm_componentElement;
				}
				this.cpm_containerElement.style.overflow = 'hidden';
				if (!this.cpm_contentElement) {
					this.cpm_contentElement = document.createElement('div');
					this.cpm_containerElement.appendChild(this.cpm_contentElement);
				}
				this.cpm_contentElement.style.whiteSpace = 'nowrap';
				this.cpm_leftElement = document.createElement('div');
				this.cpm_leftElement.style.display = 'inline-block';
				this.cpm_leftElement.style.verticalAlign = 'middle';
				this.cpm_contentElement.appendChild(this.cpm_leftElement);

				this.cpm_centerElement = document.createElement('div');
				this.cpm_centerElement.style.display = 'inline-block';
				this.cpm_centerElement.style.verticalAlign = 'middle';
				this.cpm_contentElement.appendChild(this.cpm_centerElement);

				this.cpm_rightElement = document.createElement('div');
				this.cpm_rightElement.style.display = 'inline-block';
				this.cpm_rightElement.style.verticalAlign = 'middle';
				this.cpm_contentElement.appendChild(this.cpm_rightElement);

				for (var i = 0; i < this.cpm_originalPageElements.length; i++) {
					this.cpm_centerElement.appendChild(this.cpm_originalPageElements[i]);
					this.cpm_originalPageElements[i].style.display = 'inline-block';
				}

				this.cpm_updateLeftContents();
				this.cpm_updateRightContents();
				this.cpm_scrollToCurrent();

				if (this.cpm_autoStart) {
					this.cpm_rotateInterval = window.setInterval(this.cpm_performAutoRotate, this.cpm_rotateDelay);
				}
				eventsManager.addHandler(window, 'resize',
					function(event) {
						return scope.cpm_scrollToCurrent.call(scope, event)
					}
				);
			}
		}
	};

	this.cpm_scrollToCurrent = function() {
		if (this.cpm_currentNumber !== null) {
			this.cpm_containerElement.scrollLeft = this.cpm_originalPageElements[this.cpm_currentNumber].offsetLeft - this.cpm_containerElement.offsetWidth / 2 + this.cpm_originalPageElements[this.cpm_currentNumber].offsetWidth / 2;
		} else {
			this.cpm_containerElement.scrollLeft = this.cpm_originalPageElements[0].offsetLeft - this.cpm_containerElement.offsetWidth / 2 + this.cpm_originalPageElements[0].offsetWidth / 2;
		}
	};
	this.cpm_updateLeftContents = function() {
		var i;
		if (this.cpm_leftPageElements) {
			for (i = 0; i < this.cpm_leftPageElements.length; i++) {
				this.cpm_leftElement.appendChild(this.cpm_leftPageElements[i]);
				this.cpm_leftPageElements[i].style.display = 'inline-block';
			}
		} else {
			for (i = 0; i < this.cpm_centerElement.childNodes.length; i++) {
				this.cpm_leftElement.appendChild(this.cpm_centerElement.childNodes[i].cloneNode(true));
				this.cpm_leftPageElements[i].style.display = 'inline-block';
			}
		}
	};
	this.cpm_updateRightContents = function() {
		var i;
		if (this.cpm_rightPageElements) {
			for (i = 0; i < this.cpm_rightPageElements.length; i++) {
				this.cpm_rightElement.appendChild(this.cpm_rightPageElements[i]);
				this.cpm_rightPageElements[i].style.display = 'inline-block';
			}
		} else {
			for (i = 0; i < this.cpm_centerElement.childNodes.length; i++) {
				this.cpm_rightElement.appendChild(this.cpm_centerElement.childNodes[i].cloneNode(true));
				this.cpm_rightPageElements[i].style.display = 'inline-block';
			}
		}
	};

	this.showPreviousPage = function() {
		var number = this.cpm_currentNumber - 1;
		if (number < 0) {
			number = this.cpm_originalPageElements.length - 1;
		}
		this.cpm_scope.showPage(number);
	};

	this.showNextPage = function() {
		var number = this.cpm_currentNumber + 1;
		if (number >= this.cpm_originalPageElements.length) {
			number = 0;
		}
		this.cpm_scope.showPage(number);
	};

	this.showPage = function(newNumber) {
		if (this.cpm_preloadCallBack) {
			var pageWidth = this.cpm_imageAspectRatio * this.cpm_containerElement.offsetHeight;

			var pagesAroundSide = Math.ceil((this.cpm_containerElement.offsetWidth / 2 - pageWidth / 2) / pageWidth);
			var page;
			var setPage;
			var pagesToCheck = {};
			//preload all the images along the scrolling way
			for (page = newNumber; newNumber - page <= pagesAroundSide; page--) {
				if (page < 0) {
					setPage = this.cpm_originalPageElements.length + page;
				} else {
					setPage = page;
				}
				pagesToCheck[setPage] = true;
			}
			for (page = newNumber; page <= newNumber + pagesAroundSide; page++) {
				if (page >= this.cpm_originalPageElements.length) {
					setPage = page - this.cpm_originalPageElements.length;
				} else {
					setPage = page;
				}

				pagesToCheck[setPage] = true;
			}
			if (this.cpm_currentNumber) {
				var startPage = Math.min(newNumber, this.cpm_currentNumber);
				var endPage = Math.max(newNumber, this.cpm_currentNumber);
				for (page = startPage; page <= endPage; page++) {

					pagesToCheck[page] = true;
				}
			} else {
				pagesToCheck[newNumber] = true;
			}
			for (page in pagesToCheck) {
				if (typeof this.cpm_originalPageElements[page] !== 'undefined') {
					if (!this.cpm_preloadedImagesIndex[page]) {
						this.cpm_preloadedImagesIndex[page] = false;
					}

					this.cpm_preloadCallBack(page, function(scope, preloadPage, newNumber) {
						return function() {
							scope.cpm_checkPreloadedImages.call(scope, preloadPage, newNumber)
						}
					}(this, page, newNumber));
				}
			}
		} else {
			this.cpm_showPageInside(newNumber);
		}
	};

	this.cpm_checkPreloadedImages = function(preloadedImageNumber, newNumber) {
		this.cpm_preloadedImagesIndex[preloadedImageNumber] = true;
		this.cpm_leftPageElements[preloadedImageNumber].style.display = "inline-block";
		this.cpm_originalPageElements[preloadedImageNumber].style.display = "inline-block";
		this.cpm_rightPageElements[preloadedImageNumber].style.display = "inline-block";

		var allPreloaded = true;
		for (var pageNumber in this.cpm_preloadedImagesIndex) {
			if (!this.cpm_preloadedImagesIndex[pageNumber]) {
				allPreloaded = false;
				break;
			}
		}
		if (allPreloaded) {
			this.cpm_showPageInside.call(this, newNumber);
		}
	};

	this.cpm_showPageInside = function(newNumber) {
		if (newNumber !== this.cpm_currentNumber && this.cpm_originalPageElements[newNumber]) {
			var oldNumber = this.cpm_currentNumber;
			this.cpm_currentNumber = newNumber;
			var endScrollLeft;
			var pageElementLeft = this.cpm_originalPageElements[newNumber].offsetLeft - this.cpm_containerElement.offsetWidth / 2 + this.cpm_originalPageElements[newNumber].offsetWidth / 2;
			//determine the shortest destination for scrolling
			if (Math.abs(newNumber - oldNumber) >= Math.abs(newNumber - this.cpm_originalPageElements.length - oldNumber)) {
				endScrollLeft = pageElementLeft - this.cpm_centerElement.offsetWidth;
			} else if (Math.abs(newNumber - oldNumber) >= Math.abs(newNumber + this.cpm_originalPageElements.length - oldNumber)) {
				endScrollLeft = pageElementLeft + this.cpm_centerElement.offsetWidth;
			} else {
				endScrollLeft = pageElementLeft;
			}
			var scope = this;
			TweenLite.to(this.cpm_containerElement, this.cpm_rotateSpeed, {
				'scrollLeft': endScrollLeft,
				'onComplete': function() {
					return scope.cpm_finishPageChange.call(scope);
				},
				'ease': Quad.easeInOut
			});

			if (this.cpm_onScrollFinishCallback) {
				this.cpm_onScrollFinishCallback(this.cpm_originalPageElements[oldNumber], this.cpm_originalPageElements[newNumber]);
			}
		}
	};

	this.cpm_finishPageChange = function() {
		this.cpm_scrollToCurrent();
	};

	this.stopRotation = function() {
		window.clearInterval(this.cpm_rotateInterval);
	};

	this.cpm_performAutoRotate = function() {
		this.cpm_scope.showNextPage();
	};

	this.cpm_parseOptions = function(options) {
		if (typeof options.componentElement !== 'undefined') {
			this.cpm_componentElement = options.componentElement;
		}
		if (typeof options.containerElement !== 'undefined') {
			this.cpm_containerElement = options.containerElement;
		}
		if (typeof options.contentElement !== 'undefined') {
			this.cpm_contentElement = options.contentElement;
		}
		if (typeof options.pageElements !== 'undefined') {
			this.cpm_originalPageElements = options.pageElements;
		}
		if (typeof options.sp_rotateDelay !== 'undefined') {
			this.cpm_rotateDelay = options.sp_rotateDelay;
		}
		if (typeof options.sp_rotateDelay !== 'undefined') {
			this.cpm_rotateSpeed = options.rotateSpeed;
		}
		if (typeof options.onScrollFinishCallback !== 'undefined') {
			this.cpm_onScrollFinishCallback = options.onScrollFinishCallback;
		}
		if (typeof options.leftPageElements !== 'undefined') {
			this.cpm_leftPageElements = options.leftPageElements;
		}
		if (typeof options.rightPageElements !== 'undefined') {
			this.cpm_rightPageElements = options.rightPageElements;
		}
		if (typeof options.autoStart !== 'undefined') {
			this.cpm_autoStart = options.autoStart;
		}
		if (typeof options.preloadCallBack !== "undefined") {
			this.cpm_preloadCallBack = options.preloadCallBack;
		}
		if (typeof options.imageAspectRatio !== "undefined") {
			this.cpm_imageAspectRatio = options.imageAspectRatio;
		}
	};

	return this;
};