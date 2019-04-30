window.GallerySelectorComponent = function(galleryInfo, imagesComponent) {
	var lastActiveThumbnailElement;
	var self = this;
	var componentElement;
	var centerElement;
	var thumbnailsList = [];
	var lastTimeout;
	var init = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'gallery_thumbnailsselector';

		centerElement = document.createElement('div');
		centerElement.className = 'gallery_thumbnailsselector_images';

		componentElement.appendChild(centerElement);

		var imagesInfoList = galleryInfo.getImagesList();
		for (var i = 0; i < imagesInfoList.length; i++) {
			var item = new GallerySelectorImageComponent(imagesInfoList[i], self);
			centerElement.appendChild(item.getComponentElement());
			if(i == 0) {
				var element = item.getComponentElement();
				element.classList.add('gallery_thumbnailsselector_active');
				lastActiveThumbnailElement = element;
			}
			thumbnailsList.push(item);
		}

		if (imagesInfoList.length > 3) {
			var leftButton = new GallerySelectorLeftComponent(self);
			componentElement.appendChild(leftButton.getComponentElement());

			var rightButton = new GallerySelectorRightComponent(self);
			componentElement.appendChild(rightButton.getComponentElement());
		}
		controller.addListener('galleryImageDisplay', updateEvent);

	};
	var updateEvent = function(image) {
		console.log(image);
		var element = centerElement.querySelector('.gallery_thumbnailsselector_image_' + image.getId());
		if (lastActiveThumbnailElement) {
			lastActiveThumbnailElement.classList.remove('gallery_thumbnailsselector_active');
		}
		element.classList.add('gallery_thumbnailsselector_active');
		lastActiveThumbnailElement = element;
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.scrollLeft = function() {
		if (centerElement) {
			centerElement.scrollLeft = centerElement.scrollLeft - 3;
			if (requestAnimationFrame) {
				lastTimeout = requestAnimationFrame(self.scrollLeft)
			} else {
				lastTimeout = setTimeout(self.scrollLeft, 1000 / 60);
			}
		}
	};
	this.scrollRight = function() {
		if (centerElement) {
			centerElement.scrollLeft = centerElement.scrollLeft + 3;

			if (requestAnimationFrame) {
				lastTimeout = requestAnimationFrame(self.scrollRight)
			} else {
				lastTimeout = setTimeout(self.scrollRight, 1000 / 60);
			}
		}
	};
	this.scrollStop = function() {
		if (lastTimeout) {
			if (window.cancelAnimationFrame) {
				window.cancelAnimationFrame(lastTimeout)
			} else {
				clearTimeout(lastTimeout);
			}
			lastTimeout = false;
		}
	};
	this.setSizes = function(width, height) {
		componentElement.style.height = height + 'px';
	};
	this.getGalleryHeight = function() {
		return componentElement.offsetHeight;
	};
	this.stopSlideShow = function() {
		galleryInfo.stopSlideShow();
	};
	init();
};
window.GallerySelectorImageComponent = function(imageInfo, parentComponent) {
	var componentElement;

	var init = function() {
		console.log(imageInfo);
		componentElement = document.createElement('div');
		componentElement.className = 'gallery_thumbnailsselector_image gallery_thumbnailsselector_image_' + imageInfo.getId();
		componentElement.style.backgroundImage = 'url(' + imageInfo.getThumbnailImageUrl() + ')';

		window.eventsManager.addHandler(componentElement, 'click', clickHandler)
	};
	var clickHandler = function(e) {
		parentComponent.stopSlideShow();
		imageInfo.display();
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.getId = function() {
		return imageInfo.getId();
	};
	init();
};
window.GallerySelectorLeftComponent = function(selectorObject) {
	var componentElement;
	var init = function() {
		componentElement = document.createElement('span');
		componentElement.className = 'gallery_thumbnailsselector_left';

		eventsManager.addHandler(componentElement, 'mouseover', overHandler);
		eventsManager.addHandler(componentElement, 'mouseout', outHandler);
		eventsManager.addHandler(componentElement, 'click', clickHandler);
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
	};
	var overHandler = function() {
		selectorObject.scrollLeft();
	};
	var outHandler = function() {
		selectorObject.scrollStop();
	};
	this.getComponentElement = function() {
		return componentElement;
	};

	init();
};
window.GallerySelectorRightComponent = function(selectorObject) {
	var componentElement;
	var init = function() {
		componentElement = document.createElement('span');
		componentElement.className = 'gallery_thumbnailsselector_right';

		eventsManager.addHandler(componentElement, 'mouseover', overHandler);
		eventsManager.addHandler(componentElement, 'mouseout', outHandler);
		eventsManager.addHandler(componentElement, 'click', clickHandler);
	};
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
	};
	var overHandler = function() {
		selectorObject.scrollRight();
	};
	var outHandler = function() {
		selectorObject.scrollStop();
	};
	this.getComponentElement = function() {
		return componentElement;
	};

	init();
};

//todo: remove old gallery names in 06.2017
window.SlideGallerySelectorComponent = window.GallerySelectorComponent;
window.SlideGallerySelectorItemComponent = window.GallerySelectorImageComponent;
window.SlideGalleryLeftComponent = window.GallerySelectorLeftComponent;
window.SlideGalleryRightComponent = window.GallerySelectorRightComponent;

window.ScrollGallerySelectorComponent = window.GallerySelectorComponent;
window.ScrollGallerySelectorItemComponent = window.GallerySelectorImageComponent;
window.ScrollGalleryLeftComponent = window.GallerySelectorLeftComponent;
window.ScrollGalleryRightComponent = window.GallerySelectorRightComponent;