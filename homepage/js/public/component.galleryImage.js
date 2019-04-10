window.GalleryImageComponent = function(imageInfo, parentObject, descriptionType) {
	this.title = null;
	this.link = null;
	this.preloaded = false;

	var imageOriginalWidth;
	var imageOriginalHeight;
	var galleryWidth;
	var galleryHeight;

	var componentElement;
	var imageElement;
	var infoElement;
	var hovered = false;
	var self = this;
	var clickable = false;

	var init = function() {
		createDomStructure();
		clickable = (parentObject.hasFullScreenGallery() || imageInfo.getExternalLink());
		if (clickable) {
			componentElement.className += ' gallery_image_clickable';
			eventsManager.addHandler(componentElement, eventsManager.getPointerStartEventName(), touchStart);
		}
		if (descriptionType === "overlay" && infoElement) {
			eventsManager.addHandler(componentElement, 'mouseenter', onMouseOver);
			eventsManager.addHandler(componentElement, 'mouseleave', onMouseOut);
		}
	};

	var touchStart = function(event) {
		//ignore right mouse click
		if (typeof event.which === 'undefined' || event.which !== 3) {
			eventsManager.removeHandler(componentElement, eventsManager.getPointerStartEventName(), touchStart);
			eventsManager.addHandler(componentElement, eventsManager.getPointerEndEventName(), touchEnd);
			eventsManager.addHandler(componentElement, eventsManager.getPointerMoveEventName(), touchMove);
		}
	};

	var touchMove = function(event) {
		// gallery is being massaged, don't open fullscreen gallery for this touch
		resetTouchiness();
	};

	var touchEnd = function(event) {
		resetTouchiness();
		if (imageInfo.getExternalLink()) {
			imageInfo.openExternalLink();
		} else {
			parentObject.displayFullScreenGallery();
		}
	};

	var resetTouchiness = function() {
		eventsManager.removeHandler(componentElement, eventsManager.getPointerEndEventName(), touchEnd);
		eventsManager.removeHandler(componentElement, eventsManager.getPointerMoveEventName(), touchMove);
		eventsManager.addHandler(componentElement, eventsManager.getPointerStartEventName(), touchStart);
	};
	this.destroy = function() {
		eventsManager.removeHandler(componentElement, eventsManager.getPointerStartEventName(), touchStart);
		eventsManager.removeHandler(componentElement, eventsManager.getPointerEndEventName(), touchEnd);
		eventsManager.removeHandler(componentElement, eventsManager.getPointerMoveEventName(), touchMove);
	};
	var createDomStructure = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'gallery_image';
		componentElement.style.display = 'none';

		imageElement = document.createElement('img');
		imageElement.style.visibility = 'hidden';
		componentElement.appendChild(imageElement);

		if (descriptionType === "overlay" && (imageInfo.getDescription() || imageInfo.getTitle())) {
			infoElement = self.makeElement("div", "gallery_image_info", componentElement);
			self.makeElement("div", "gallery_image_info_background", infoElement);

			var info;
			if (info = imageInfo.getTitle()) {
				var titleElement = self.makeElement("div", "gallery_image_title", infoElement);
				titleElement.innerHTML = info;
			}
			if (info = imageInfo.getDescription()) {
				var descriptionElement = self.makeElement("div", "gallery_image_description", infoElement);
				descriptionElement.innerHTML = info;
			}
		}
	};

	var showInfo = function() {
		if (hovered) {
			domHelper.addClass(infoElement, "gallery_image_info_visible");
			TweenLite.to(infoElement, 0.5, {'css': {'opacity': 1}});
		}
	};

	var hideInfo = function() {
		TweenLite.to(infoElement, 0.25, {
			'css': {'opacity': 0}, 'onComplete': function() {
				domHelper.removeClass(infoElement, "gallery_image_info_visible");
			}
		});
	};

	var onMouseOver = function() {
		hovered = true;
		if (infoElement) {
			executeAfterImageHasLoaded(showInfo);
		}
	};

	var onMouseOut = function() {
		hovered = false;
		if (infoElement) {
			hideInfo();
		}
	};

	this.checkPreloadImage = function(callBack) {
		if (!imageElement.src) {
			imageElement.src = imageInfo.getBigImageUrl();
			imageElement.style.visibility = 'hidden';
			componentElement.style.display = '';
		}
		if (!imageElement.complete) {
			window.setTimeout(function(callBack) {
				return function() {
					self.checkPreloadImage(callBack);
				};
			}(callBack), 100);
		} else {
			if (!self.preloaded) {
				imageElement.style.visibility = 'visible';
				imageOriginalWidth = imageElement.offsetWidth;
				imageOriginalHeight = imageElement.offsetHeight;
				componentElement.style.display = 'none';
				self.preloaded = true;
				resizeImageElement();
			}

			if (callBack) {
				callBack();
			}
		}
	};
	this.displayComponentElement = function() {
		componentElement.style.display = '';
	};
	this.resize = function(imagesContainerWidth, imagesContainerHeight) {
		galleryWidth = imagesContainerWidth;
		galleryHeight = imagesContainerHeight;

		resizeImageElement();
	};
	var resizeImageElement = function() {
		if (galleryWidth && galleryHeight) {
			componentElement.style.width = galleryWidth + 'px';
			componentElement.style.height = galleryHeight + 'px';

			if (imageOriginalWidth && imageOriginalHeight) {
				var imageWidth, imageHeight;
				var positionTop = 0, positionLeft = 0;

				var logic = imageInfo.getImageResizeType();
				var aspectRatio = imageOriginalWidth / imageOriginalHeight;
				if (logic === "fill") {
					imageHeight = galleryHeight;
					imageWidth = imageHeight * aspectRatio;
					if (imageWidth < galleryWidth) {
						imageWidth = galleryWidth;
						imageHeight = imageWidth / aspectRatio;
					}
					// centering
					if (imageHeight > galleryHeight) {
						positionTop = (imageHeight - galleryHeight) / -2;
					}
					if (imageWidth > galleryWidth) {
						positionLeft = (imageWidth - galleryWidth) / -2;
					}
				} else {
					imageWidth = imageOriginalWidth;
					imageHeight = imageOriginalHeight;

					if (imageWidth > galleryWidth) {
						imageWidth = galleryWidth;
						imageHeight = imageWidth / aspectRatio;
					}

					if (imageHeight > galleryHeight) {
						imageHeight = galleryHeight;
						imageWidth = imageHeight * aspectRatio;
					}
					positionTop = (galleryHeight - imageHeight) / 2;
					positionLeft = (galleryWidth - imageWidth) / 2;
				}
				if (imageElement) {
					imageElement.style.width = imageWidth + 'px';
					imageElement.style.height = imageHeight ? imageHeight + 'px' : "";
					imageElement.style.left = positionLeft + 'px';
					imageElement.style.top = positionTop + 'px';
				}
			}
		}
	};

	var executeAfterImageHasLoaded = function(callBack) {
		if (imageElement.complete) {
			callBack();
		} else {
			window.setTimeout(executeAfterImageHasLoaded, 100);
		}
	};

	this.getComponentElement = function() {
		return componentElement;
	};
	this.getImageElement = function() {
		return imageElement;
	};
	this.getId = function() {
		return imageInfo.getId();
	};

	this.activate = function() {
		imageInfo.display();
	};
	this.getImageInfo = function() {
		return imageInfo;
	};
	init();
};
DomElementMakerMixin.call(GalleryImageComponent.prototype);