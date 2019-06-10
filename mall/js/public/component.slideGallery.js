(function() {
    window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
    window.cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame || window.webkitCancelAnimationFrame || window.msCancelAnimationFrame;
})();
window.SlideGalleryComponent = function(componentElement, galleryInfo) {
    var self = this;
    var selectorComponent;
    var imagesComponent;
    var descriptionComponent;
    var buttonsContainerElement;

    var init = function() {
        domHelper.addClass(componentElement, 'slide_gallery');
        var imagesList = galleryInfo.getImagesList();
        if (imagesList) {
            initDomStructure();
            if (galleryInfo.getDisplaySelector()) {
                initGallerySelector();
            }
            //size of containers should be recalculated BEFORE images begin preloading, because otherwise images
            //will be displayed in zero-height block and won't get true offsetHeight/offsetWidth
            self.recalculateSizes();
            eventsManager.addHandler(window, 'resize', self.recalculateSizes);
        }
    };
    var initDomStructure = function() {
        while (componentElement.firstChild) {
            componentElement.removeChild(componentElement.firstChild);
        }

        imagesComponent = new SlideGalleryImagesComponent(galleryInfo, self);
        componentElement.appendChild(imagesComponent.getComponentElement());

        // add buttons
        var imagesInfosList = galleryInfo.getImagesList();
        var imageNumber = 0;
        buttonsContainerElement = document.createElement('div');

        var imagesPrevNextButtonsEnabled = galleryInfo.areImagesPrevNextButtonsEnabled();
        var imagesButtonsEnabled = galleryInfo.areImagesButtonsEnabled();
        var playbackButtonEnabled = galleryInfo.isPlaybackButtonEnabled();
        var button;
        if (playbackButtonEnabled || imagesButtonsEnabled || imagesPrevNextButtonsEnabled) {
            buttonsContainerElement.className = 'slide_gallery_buttons';
            componentElement.appendChild(buttonsContainerElement);
        }

        if (imagesPrevNextButtonsEnabled) {
            var prevSlideButton = new SlideGalleryPrevButtonComponent(imagesComponent);
            componentElement.appendChild(prevSlideButton.getComponentElement());
        }

        if (imagesButtonsEnabled) {
            for (var i = 0; i <= imagesInfosList.length; i++) {
                if (imagesInfosList[i]) {
                    button = new SlideGalleryButtonComponent(imageNumber, imagesComponent, imagesInfosList[i]);
                    buttonsContainerElement.appendChild(button.getComponentElement());
                    imageNumber++;
                }
            }
        }

        if (imagesPrevNextButtonsEnabled) {
            var prevNextButton = new SlideGalleryNextButtonComponent(imagesComponent);
            componentElement.appendChild(prevNextButton.getComponentElement());
        }

        if (galleryInfo.getDescriptionType() == 'static') {
            descriptionComponent = new SlideGalleryDescriptionComponent(galleryInfo);
            componentElement.appendChild(descriptionComponent.getComponentElement());
            descriptionComponent.setDescription(imagesInfosList[0]);
        }

        if (playbackButtonEnabled) // only add if automatic sliding is enabled
        {
            button = new SlideGalleryPlaybackButtonComponent(imagesComponent);
            buttonsContainerElement.appendChild(button.getComponentElement());
        }
    };
    this.getDescriptionComponent = function() {
        return descriptionComponent;
    };
    this.getButtonsContainer = function() {
        return buttonsContainerElement;
    };
    this.getSelectorComponent = function() {
        return selectorComponent;
    };
    this.recalculateSizes = function() {
        var height;
        var galleryHeight;
        var imagesHeight;
        var width = componentElement.offsetWidth;
        if (galleryInfo.getHeightLogics() == 'viewport') {
            var viewPortHeight = window.innerHeight ? window.innerHeight : document.documentElement.offsetHeight;
            galleryHeight = viewPortHeight * 0.7;
        } else if (galleryInfo.getHeightLogics() == 'imagesHeight') {
            imagesHeight = galleryInfo.getHeight();
        } else if (galleryInfo.getHeightLogics() == 'aspected') {
            var aspect = galleryInfo.getHeight();
            imagesHeight = width * aspect;
        } else {
            galleryHeight = galleryInfo.getHeight();
            if (!galleryHeight) {
                galleryHeight = componentElement.offsetHeight;
            }
        }

        if (galleryHeight) {
            if (selectorComponent) {
                height = galleryHeight - selectorComponent.getHeight();
            } else {
                height = galleryHeight;
            }
        } else if (imagesHeight) {
            height = imagesHeight;
        }

        imagesComponent.setSizes(width, height);
    };
    var initGallerySelector = function() {
        selectorComponent = new SlideGallerySelectorComponent(galleryInfo, imagesComponent);
        componentElement.appendChild(selectorComponent.getComponentElement());
    };
    init();
};

window.SlideGalleryPrevButtonComponent = function(imagesComponent) {
    var componentElement;
    var init = function() {
        componentElement = document.createElement('span');
        componentElement.className = 'button_prev_slide slide_controller_buttons';
        componentElement.innerHTML = 'Prev';
        eventsManager.addHandler(componentElement, 'click', onClick);
    };

    var onClick = function() {
        imagesComponent.stopSlideShow();
        return imagesComponent.showPreviousSlide.call(imagesComponent);
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    init();
};

window.SlideGalleryNextButtonComponent = function(imagesComponent) {
    var componentElement;
    var init = function() {
        componentElement = document.createElement('span');
        componentElement.className = 'button_next_slide slide_controller_buttons';
        componentElement.innerHTML = 'Next';
        eventsManager.addHandler(componentElement, 'click', onClick);
    };

    var onClick = function() {
        imagesComponent.stopSlideShow();
        return imagesComponent.showNextSlide.call(imagesComponent);
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    init();
};

window.SlideGalleryImagesComponent = function(galleryInfo, parentComponent) {
    var componentElement;
    var self = this;
    var imagesList = [];
    var imagesIndex = [];
    var currentImageNumber;
    var width;
    var height;

    var fullScreenGallery;
    var preloaded;

    var init = function() {
        if (galleryInfo.getImagesList()) {
            initDomStructure();

            if (imagesList.length > 0) {
                controller.addListener('startApplication', preloadAllImages);
            }
            if (galleryInfo.getFullScreenGallery()) {
                fullScreenGallery = new FullScreenGalleryComponent(galleryInfo);
            }
            controller.addListener('galleryImageDisplay', galleryImageDisplayHandler);
        }
    };

    var initDomStructure = function() {
        componentElement = document.createElement('div');
        componentElement.className = 'slide_gallery_images';

        var imagesInfoList = galleryInfo.getImagesList();
        for (var i = 0; i < imagesInfoList.length; i++) {
            var imageItem = new SlideGalleryItem(imagesInfoList[i], self, galleryInfo.getDescriptionType());
            componentElement.appendChild(imageItem.getComponentElement());

            imagesList.push(imageItem);
            imagesIndex[imageItem.getId()] = imageItem;
            imagesIndex[imageItem.getId()].number = i;
        }
    };
    var preloadAllImages = function() {
        for (var i = imagesList.length; i--;) {
            imagesList[i].checkPreloadImage(checkImagesPreloadStatus);
        }
    };
    var checkImagesPreloadStatus = function() {
        preloaded = true;
        for (var i = imagesList.length; i--;) {
            if (!imagesList[i].preloaded) {
                preloaded = false;
            }
        }
        if (preloaded) {
            startApplication();
        }
    };

    var startApplication = function() {
        parentComponent.recalculateSizes();
        self.initSlides({
            'componentElement': componentElement,
            'interval': galleryInfo.getChangeDelay(),
            'changeDuration': 1,
            'onSlideChange': onImageChange,
            'heightCalculated': false,
        });
        currentImageNumber = 0;
    };

    var galleryImageDisplayHandler = function(imageObject) {
        var imageId = imageObject.getId();
        if (imagesIndex[imageId]) {
            switchImage(imageId);
        }
    };

    var switchImage = function(newImageId) {
        if (imagesIndex[newImageId]) {
            self.showSlide(imagesIndex[newImageId].number);
        }
    };

    var onImageChange = function(number) {
        imagesList[number].activate();
        currentImageNumber = number;
    };

    this.setSizes = function(newWidth, newHeight) {
        width = componentElement.offsetWidth;
        height = newHeight;
        componentElement.style.height = height + 'px';

        if (preloaded) {
            for (var i = imagesList.length; i--;) {
                imagesList[i].resize(width, height);
            }
        }
    };

    this.hasFullScreenGallery = function() {
        return galleryInfo.getFullScreenGallery();
    };

    this.displayFullScreenGallery = function() {
        if (fullScreenGallery) {
            self.stopSlideShow();
            fullScreenGallery.display();
        }
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    init();
};
SlidesMixin.call(SlideGalleryImagesComponent.prototype);

window.SlideGalleryItem = function(imageInfo, parentObject, descriptionType) {
    this.title = null;
    this.link = null;
    this.preloaded = false;
    this.number = 0;

    var imageOriginalWidth;
    var imageOriginalHeight;

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
            componentElement.className += ' slide_gallery_item_clickable';
            eventsManager.addHandler(componentElement, 'click', clickHandler);
        }
        if (descriptionType == 'overlay' && infoElement) {
            eventsManager.addHandler(componentElement, 'mouseenter', onMouseOver);
            eventsManager.addHandler(componentElement, 'mouseleave', onMouseOut);
        }
    };

    var createDomStructure = function() {
        componentElement = document.createElement('div');
        componentElement.className = 'slide_gallery_item slide';

        imageElement = document.createElement('img');
        imageElement.src = imageInfo.getBigImageUrl();
        imageElement.style.visibility = 'hidden';
        componentElement.appendChild(imageElement);

        if (descriptionType == 'overlay' && (imageInfo.getDescription() || imageInfo.getTitle())) {
            infoElement = self.makeElement('div', 'slide_gallery_item_info', componentElement);
            self.makeElement('div', 'slide_gallery_item_info_background', infoElement);

            var info;
            if (info = imageInfo.getTitle()) {
                var titleElement = self.makeElement('div', 'slide_gallery_item_title', infoElement);
                titleElement.innerHTML = info;
            }
            if (info = imageInfo.getDescription()) {
                var descriptionElement = self.makeElement('div', 'slide_gallery_item_description', infoElement);
                descriptionElement.innerHTML = info;
            }
        }
    };

    var clickHandler = function() {
        if (imageInfo.getExternalLink()) {
            document.location.href = imageInfo.getExternalLink();
        } else {
            parentObject.displayFullScreenGallery();
        }
    };

    var showInfo = function() {
        if (hovered) {
            domHelper.addClass(infoElement, 'slide_gallery_item_info_visible');
            TweenLite.to(infoElement, 0.5, {'css': {'opacity': 1}});
        }
    };

    var hideInfo = function() {
        TweenLite.to(infoElement, 0.25, {
            'css': {'opacity': 0}, 'onComplete': function() {
                domHelper.removeClass(infoElement, 'slide_gallery_item_info_visible');
            },
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

    this.display = function() {
        parentObject.showSlide(self.number);
    };

    this.checkPreloadImage = function(callBack) {
        if (!imageElement.complete) {
            window.setTimeout(function(callBack) {
                return function() {
                    self.checkPreloadImage(callBack);
                };
            }(callBack), 100);
        } else {
            if (!imageOriginalWidth && !imageOriginalHeight) {
                imageElement.style.visibility = 'visible';
                imageOriginalWidth = imageElement.offsetWidth;
                imageOriginalHeight = imageElement.offsetHeight;
            }
            self.preloaded = true;
            componentElement.style.display = 'none';
            callBack();
        }
    };

    this.resize = function(imagesContainerWidth, imagesContainerHeight) {
        var imageWidth, imageHeight;
        var positionTop = 0, positionLeft = 0;

        var logic = imageInfo.getImageResizeLogics();
        if (!imageOriginalWidth && !imageOriginalHeight && imageElement) {
            imageOriginalWidth = imageElement.offsetWidth;
            imageOriginalHeight = imageElement.offsetHeight;
        }
        var aspectRatio = imageOriginalWidth / imageOriginalHeight;

        if (logic == 'fit') {
            imageHeight = imagesContainerHeight;
            imageWidth = imageHeight * aspectRatio;
            if (imageWidth < imagesContainerWidth) {
                imageWidth = imagesContainerWidth;
                imageHeight = imageWidth / aspectRatio;
            }
            // centering
            if (imageHeight > imagesContainerHeight) {
                positionTop = (imageHeight - imagesContainerHeight) / -2;
            }
            if (imageWidth > imagesContainerWidth) {
                positionLeft = (imageWidth - imagesContainerWidth) / -2;
            }
        } else {
            imageWidth = imageOriginalWidth;
            imageHeight = imageOriginalHeight;

            if (imageWidth > imagesContainerWidth) {
                imageWidth = imagesContainerWidth;
                imageHeight = imageWidth / aspectRatio;
            }

            if (imageHeight > imagesContainerHeight) {
                imageHeight = imagesContainerHeight;
                imageWidth = imageHeight * aspectRatio;
            }
            positionTop = (imagesContainerHeight - imageHeight) / 2;
            positionLeft = (imagesContainerWidth - imageWidth) / 2;
        }
        componentElement.style.width = imagesContainerWidth + 'px';
        componentElement.style.height = imagesContainerHeight + 'px';

        if (imageElement) {
            imageElement.style.width = imageWidth + 'px';
            imageElement.style.height = imageHeight ? imageHeight + 'px' : '';
            imageElement.style.left = positionLeft + 'px';
            imageElement.style.top = positionTop + 'px';
            if (infoElement) {
                infoElement.style.width = imageWidth + 'px';
                infoElement.style.left = positionLeft + 'px';
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
    init();
};
DomElementMakerMixin.call(SlideGalleryItem.prototype);

window.SlideGalleryDescriptionComponent = function(galleryInfo) {
    var componentElement, titleElement, descriptionElement;
    var self = this;

    var init = function() {
        createDomStructure();
        controller.addListener('galleryImageDisplay', onImageDisplay);
    };
    var createDomStructure = function() {
        componentElement = self.makeElement('div', 'slide_gallery_description');
        titleElement = self.makeElement('div', 'slide_gallery_description_title', componentElement);
        descriptionElement = self.makeElement('div', 'slide_gallery_descripion_description', componentElement);
    };
    var onImageDisplay = function() {
        var currentImageInfo = galleryInfo.getCurrentImage();
        self.setDescription(currentImageInfo);
    };
    this.setDescription = function(imageInfo) {
        var content;
        if (content = imageInfo.getTitle()) {
            titleElement.innerHTML = content;
            titleElement.style.display = 'block';
        } else {
            titleElement.style.display = 'none';
        }
        if (content = imageInfo.getDescription()) {
            descriptionElement.innerHTML = content;
            descriptionElement.style.display = 'block';
        } else {
            descriptionElement.style.display = 'none';
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(SlideGalleryDescriptionComponent.prototype);

window.SlideGalleryButtonComponent = function(number, imagesComponent, buttonImageObject) {
    var componentElement;
    var numberElement;
    var self = this;

    var init = function() {
        createDomStructure();
        eventsManager.addHandler(componentElement, 'click', onClick);
        controller.addListener('galleryImageDisplay', galleryImageDisplayHandler);

        if (number == 0) {
            self.activate();
        }
    };
    var createDomStructure = function() {
        componentElement = self.makeElement('div', 'slide_gallery_button');
        numberElement = self.makeElement('span', 'slide_gallery_button_number');
        numberElement.innerHTML = number + 1;
        componentElement.appendChild(numberElement);
    };
    var onClick = function() {
        imagesComponent.stopSlideShow();
        imagesComponent.showSlide(number);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    var galleryImageDisplayHandler = function(imageObject) {
        if (buttonImageObject == imageObject) {
            self.activate();
        } else {
            self.deActivate();
        }
    };
    this.activate = function() {
        domHelper.addClass(componentElement, 'slide_gallery_button_active');
    };
    this.deActivate = function() {
        domHelper.removeClass(componentElement, 'slide_gallery_button_active');
    };
    init();
};
DomElementMakerMixin.call(SlideGalleryButtonComponent.prototype);

window.SlideGalleryPlaybackButtonComponent = function(imagesComponent) {
    var componentElement;
    var self = this;
    var playbackEnabled = true;

    var init = function() {
        createDomStructure();
        eventsManager.addHandler(componentElement, 'click', onClick);
        controller.addListener('slidesPlaybackUpdate', onSlidesPlaybackUpdate);
    };
    var createDomStructure = function() {
        componentElement = self.makeElement('div', 'slide_gallery_button slide_gallery_button_pause');
    };
    var onClick = function() {
        if (playbackEnabled) {
            imagesComponent.stopSlideShow();
            playbackEnabled = false;
            updateStyle();
        } else {
            imagesComponent.startSlideShow(true);
            playbackEnabled = true;
            updateStyle();
        }
    };
    var onSlidesPlaybackUpdate = function(slidesComponentElement) {
        if (slidesComponentElement == imagesComponent.getComponentElement()) {
            playbackEnabled = imagesComponent.slideShowEnabled;
            updateStyle();
        }
    };
    var updateStyle = function() {
        if (!playbackEnabled) {
            domHelper.removeClass(componentElement, 'slide_gallery_button_pause');
            domHelper.addClass(componentElement, 'slide_gallery_button_play');
        } else {
            domHelper.removeClass(componentElement, 'slide_gallery_button_play');
            domHelper.addClass(componentElement, 'slide_gallery_button_pause');
        }
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(SlideGalleryPlaybackButtonComponent.prototype);

window.SlideGallerySelectorComponent = function(galleryInfo, imagesComponent) {
    var self = this;
    var componentElement;
    var centerElement;
    var thumbnailsList = [];
    var lastTimeout;
    var init = function() {
        componentElement = document.createElement('div');
        componentElement.className = 'slide_gallery_selector';

        centerElement = document.createElement('div');
        centerElement.className = 'slide_gallery_selector_images';

        componentElement.appendChild(centerElement);

        var imagesInfoList = galleryInfo.getImagesList();
        for (var i = 0; i < imagesInfoList.length; i++) {
            var item = new SlideGallerySelectorItemComponent(imagesInfoList[i], self);
            centerElement.appendChild(item.getComponentElement());

            thumbnailsList.push(item);
        }

        if (imagesInfoList.length > 3) {
            var leftButton = new SlideGalleryLeftComponent(self);
            componentElement.appendChild(leftButton.getComponentElement());

            var rightButton = new SlideGalleryRightComponent(self);
            componentElement.appendChild(rightButton.getComponentElement());
        }

    };
    this.getComponentElement = function() {
        return componentElement;
    };
    this.scrollLeft = function() {
        if (centerElement) {
            centerElement.scrollLeft = centerElement.scrollLeft - 3;
            if (requestAnimationFrame) {
                lastTimeout = requestAnimationFrame(self.scrollLeft);
            } else {
                lastTimeout = setTimeout(self.scrollLeft, 1000 / 60);
            }
        }
    };
    this.scrollRight = function() {
        if (centerElement) {
            centerElement.scrollLeft = centerElement.scrollLeft + 3;

            if (requestAnimationFrame) {
                lastTimeout = requestAnimationFrame(self.scrollRight);
            } else {
                lastTimeout = setTimeout(self.scrollRight, 1000 / 60);
            }
        }
    };
    this.scrollStop = function() {
        if (lastTimeout) {
            if (window.cancelAnimationFrame) {
                window.cancelAnimationFrame(lastTimeout);
            } else {
                clearTimeout(lastTimeout);
            }
            lastTimeout = false;
        }
    };
    this.setSizes = function(width, height) {
        componentElement.style.height = height + 'px';
    };
    this.getHeight = function() {
        return componentElement.offsetHeight;
    };
    this.stopSlideShow = function() {
        imagesComponent.stopSlideShow();
    };
    init();
};
window.SlideGallerySelectorItemComponent = function(imageInfo, parentComponent) {
    var componentElement;
    var imageElement;

    var init = function() {
        componentElement = document.createElement('div');
        componentElement.className = 'slidegallery_selector_item';
        imageElement = document.createElement('img');
        imageElement.src = imageInfo.getThumbnailImageUrl();
        imageElement.removeAttribute('width');
        imageElement.removeAttribute('height');
        componentElement.appendChild(imageElement);

        window.eventsManager.addHandler(componentElement, 'click', clickHandler);
    };
    var clickHandler = function() {
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
window.SlideGalleryLeftComponent = function(selectorObject) {
    var componentElement;
    var init = function() {
        componentElement = document.createElement('span');
        componentElement.className = 'slide_gallery_selector_left';

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
window.SlideGalleryRightComponent = function(selectorObject) {
    var componentElement;
    var init = function() {
        componentElement = document.createElement('span');
        componentElement.className = 'slide_gallery_selector_right';

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