window.GalleryComponent = function(componentElement, galleryInfo, type) {
    var self = this;
    var selectorComponent;
    var imagesComponent;
    var descriptionComponent;
    var buttonsContainerElement;
    var buttonPrevious;
    var buttonNext;
    var preloadedStructureElement;
    var imageButtons = [];

    this.init = function() {
        if (imagesComponent) {
            imagesComponent.startApplication();
        }
    };
    var construct = function() {
        domHelper.addClass(componentElement, 'gallery_' + type);
        var imagesList = galleryInfo.getImagesList();
        if (imagesList) {
            makeDomStructure();
            if (galleryInfo.isThumbnailsSelectorEnabled()) {
                initGallerySelector();
            }
            self.recalculateSizes();
            window.addEventListener('resize', self.recalculateSizes);
            window.addEventListener('orientationchange', self.recalculateSizes);
        }
    };
    var makeDomStructure = function() {
        while (componentElement.firstChild) {
            var structureChild = componentElement.firstChild;
            if ((typeof structureChild.className != 'undefined') && (structureChild.className.indexOf('gallery_structure') >= 0)) {
                preloadedStructureElement = structureChild;
            }
            componentElement.removeChild(componentElement.firstChild);
        }
        if (preloadedStructureElement) {
            componentElement.appendChild(preloadedStructureElement);
        }
        if (type == 'scroll') {
            imagesComponent = new GalleryImagesScrollComponent(galleryInfo, self);
        } else if (type == 'slide') {
            imagesComponent = new GalleryImagesSlideComponent(galleryInfo, self);
        } else if (type == 'carousel') {
            imagesComponent = new GalleryImagesCarouselComponent(galleryInfo, self);
        }
        if (preloadedStructureElement) {
            var gallery_images_container = create('gallery_images_container');
            gallery_images_container.appendChild(imagesComponent.getComponentElement());
        } else {
            componentElement.appendChild(imagesComponent.getComponentElement());
        }
        // add buttons
        var imagesInfosList = galleryInfo.getImagesList();
        var imageNumber = 0;

        var imagesPrevNextButtonsEnabled = galleryInfo.areImagesPrevNextButtonsEnabled();
        var imagesPrevNextButtonsSeparated = galleryInfo.areImagesPrevNextButtonsSeparated();
        var imagesButtonsEnabled = galleryInfo.areImagesButtonsEnabled();
        var playbackButtonEnabled = galleryInfo.isPlaybackButtonEnabled();
        var fullScreenButtonEnabled = galleryInfo.isFullScreenButtonEnabled();
        var button;
        if (playbackButtonEnabled || imagesButtonsEnabled || imagesPrevNextButtonsEnabled || fullScreenButtonEnabled) {

            buttonsContainerElement = create('gallery_buttons');

            if (imagesPrevNextButtonsSeparated && imagesPrevNextButtonsEnabled) {
                var prevNextButtonsContainerElement = create('gallery_buttons_prevnext');
            }

            if (imagesPrevNextButtonsEnabled) {
                buttonPrevious = new GalleryPreviousButtonComponent(galleryInfo);

                if (imagesPrevNextButtonsSeparated) {
                    prevNextButtonsContainerElement.appendChild(buttonPrevious.getComponentElement());
                } else {
                    buttonsContainerElement.appendChild(buttonPrevious.getComponentElement());
                }
            }

            if (imagesButtonsEnabled) {
                for (var i = 0; i <= imagesInfosList.length; i++) {
                    if (imagesInfosList[i]) {
                        button = new GalleryButtonComponent(imagesInfosList[i], galleryInfo);
                        buttonsContainerElement.appendChild(button.getComponentElement());
                        imageNumber++;
                        imageButtons.push(button);
                    }
                }
            }

            if (imagesPrevNextButtonsEnabled) {
                buttonNext = new GalleryNextButtonComponent(galleryInfo);

                if (imagesPrevNextButtonsSeparated) {
                    prevNextButtonsContainerElement.appendChild(buttonNext.getComponentElement());
                } else {
                    buttonsContainerElement.appendChild(buttonNext.getComponentElement());
                }
            }

            if (playbackButtonEnabled) {
                button = new GalleryPlaybackButtonComponent(galleryInfo);
                buttonsContainerElement.appendChild(button.getComponentElement());
                imageButtons.push(button);
            }
            if (fullScreenButtonEnabled) {
                button = new GalleryFullScreenButtonComponent(galleryInfo, imagesComponent);
                buttonsContainerElement.appendChild(button.getComponentElement());
                imageButtons.push(button);
            }
        }

        if (galleryInfo.getDescriptionType() === 'static') {
            descriptionComponent = new GalleryDescriptionComponent(galleryInfo);
            if (preloadedStructureElement) {
                var gallery_description_container = create('gallery_description_container');
                gallery_description_container.appendChild(descriptionComponent.getComponentElement());
            } else {
                componentElement.appendChild(descriptionComponent.getComponentElement());
            }
            descriptionComponent.setDescription(imagesInfosList[0]);
        }

    };

    var create = function(className) {
        //we add new element to componentElement or to div.gallery_structure if it exists in html
        //if element with className already defined in html(as div.gallery_structure child or sub child) return him
        var newElement = document.createElement('div');
        newElement.className = className;

        if (preloadedStructureElement) {
            var definedElement = findChild(preloadedStructureElement, className);
            if (definedElement) {
                newElement = definedElement;
            } else {
                preloadedStructureElement.appendChild(newElement);
            }
        } else {
            componentElement.appendChild(newElement);
        }

        return newElement;
    };

    var findChild = function(element, className) {
        for (var i = 0; i < element.childNodes.length; i++) {
            var child = element.childNodes[i];
            if ((typeof child.className !== 'undefined') && (child.className.indexOf(className) >= 0)) {
                return child;
            }
            var result;
            if (result = findChild(child, className)) {
                return result;
            }
        }
        return false;
    };
    this.destroy = function() {
        eventsManager.removeHandler(window, 'resize', self.recalculateSizes);
        controller.removeListener('startApplication', imagesComponent.startApplication);

        if (imagesComponent) {
            imagesComponent.destroy();
        }
        if (buttonPrevious) {
            buttonPrevious.destroy();
        }
        if (buttonNext) {
            buttonNext.destroy();
        }
        if (descriptionComponent) {
            descriptionComponent.destroy();
        }
        for (var i = 0; i < imageButtons.length; i++) {
            imageButtons[i].destroy();
        }
    };
    this.getImagesComponent = function() {
        return imagesComponent;
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
        var isMobile = window.innerWidth < 768;
        var imagesComponentHeight;
        var computedStyle;
        if (typeof window.getComputedStyle !== 'undefined') {
            computedStyle = window.getComputedStyle(componentElement);
        } else {
            computedStyle = componentElement.currentStyle;
        }
        var galleryWidth = componentElement.offsetWidth - parseFloat(computedStyle.paddingLeft) - parseFloat(computedStyle.paddingRight);
        var galleryHeight;

        var galleryHeightSetting = galleryInfo.getGalleryHeight(isMobile);
        var galleryResizeType = galleryInfo.getGalleryResizeType(isMobile);
        if (galleryResizeType === 'imagesHeight') {
            imagesComponentHeight = galleryHeightSetting;
        } else if (galleryResizeType === 'aspected') {
            var aspect = galleryHeightSetting;
            imagesComponentHeight = galleryWidth * aspect;
        } else if (galleryResizeType === 'viewport') {
            var viewPortHeight = window.innerHeight ? window.innerHeight : document.documentElement.offsetHeight;
            galleryHeight = galleryHeightSetting;
            if (galleryHeight && (typeof galleryHeight === 'string') && galleryHeight.indexOf('%') > -1) {
                galleryHeight = viewPortHeight * parseFloat(galleryHeight) / 100;
            } else {
                galleryHeight = viewPortHeight * galleryHeight;
            }
        } else {
            galleryHeight = galleryHeightSetting;
            if (!galleryHeight) {
                galleryHeight = componentElement.offsetHeight - parseFloat(computedStyle.paddingTop) - parseFloat(computedStyle.paddingBottom);
            }
        }
        if (galleryHeight) {
            if (selectorComponent) {
                var selectorHeight = galleryInfo.getThumbnailsSelectorHeight();
                if (selectorHeight) {
                    if (selectorHeight.indexOf('%') > -1) {
                        selectorHeight = galleryHeight * parseFloat(selectorHeight) / 100;
                    }
                    selectorComponent.setSizes(galleryWidth, selectorHeight);
                }
                imagesComponentHeight = galleryHeight - selectorComponent.getGalleryHeight();
            } else {
                imagesComponentHeight = galleryHeight;
            }
        }
        imagesComponent.setSizes(galleryWidth, imagesComponentHeight);
    };
    this.getButtonNextComponent = function() {
        return buttonNext;
    };
    this.getButtonPreviousComponent = function() {
        return buttonPrevious;
    };
    var initGallerySelector = function() {
        if (typeof GallerySelectorComponent !== 'undefined') {
            selectorComponent = new GallerySelectorComponent(galleryInfo, imagesComponent);
            componentElement.appendChild(selectorComponent.getComponentElement());
        }
    };
    construct();
};