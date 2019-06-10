window.ProductGalleryBComponent = function(componentElement, galleryInfo) {

    var self = this;
    this.componentElement = null;

    var descriptionElement;
    var descriptionTitleElement;
    var descriptionContentElement;
    var scrollButtonNext;
    var scrollButtonPrev;
    var scrollContainer;
    var imagesContainer;
    var headerGalleryPopup;
    var imageSwitchers = [];

    var galleryImageElements = [];
    var galleryImagesList = [];
    var galleryImagesIndex = {};
    this.activeImageIndex = 0;

    this.init = function() {
        self.componentElement = componentElement;
        createDomStructure();
        displayComponent();

        controller.addListener('headerGalleryImageChanged', imageChange);
        self.scrollPagesInit({
            'componentElement': scrollContainer,
            'rotateDelay': 10000,
            'pageElements': galleryImageElements,
            'autoStart': true,
            'changeDuration': 1,
            'effectDuration': 1.5,
            'preloadCallBack': preloadCallBack,
            'onPageChangeCallback': this.onPageChangeCallback,
        });
        updateDescriptionContent();
        if (galleryInfo.getHeightLogics() !== 'containerHeight') {
            eventsManager.addHandler(window, 'resize', resize);
            resize();
        }
        self.showPage(0);
    };
    var preloadCallBack = function(number, callback) {
        if (typeof galleryImagesList[number] !== 'undefined') {
            galleryImagesList[number].checkPreloadImage(callback);
        }
    };

    var resize = function() {
        var heightLogics = galleryInfo.getHeightLogics();
        var height = 0;
        if (heightLogics === 'imagesAspected') {
            var aspectRatio = galleryInfo.getHeight();
            height = scrollContainer.offsetWidth * aspectRatio;
        }
        if (height > 0) {
            scrollContainer.style.height = height + 'px';
        }
    };
    this.onPageChangeCallback = function(number) {
        self.activeImageIndex = number;
        updateDescriptionContent();
        imageChange();
    };
    var updateDescriptionContent = function() {
        if (descriptionElement) {
            var title = galleryInfo.galleryImagesList[self.activeImageIndex].getTitle();
            if (title && title !== '') {
                descriptionTitleElement.innerHTML = title;
                descriptionTitleElement.style.display = '';
            } else {
                descriptionTitleElement.innerHTML = '';
                descriptionTitleElement.style.display = 'none';
            }
            var content = galleryInfo.galleryImagesList[self.activeImageIndex].getDescription();
            if (content && content !== '') {
                descriptionContentElement.innerHTML = content;
                descriptionContentElement.style.display = '';
            } else {
                descriptionContentElement.innerHTML = '';
                descriptionContentElement.style.display = 'none';
            }
        }
    };

    this.selectPlaceMark = function(placeMarkInfo, currentPlaceMark) {
        self.stopPagesRotation();
        var products = placeMarkInfo.getProducts();
        headerGalleryPopup.setCurrentPlaceMarkComponent(currentPlaceMark, products);
    };
    var createDomStructure = function() {
        // description block
        if (galleryInfo.isStaticDescriptionEnabled()) {
            descriptionElement = document.createElement('div');
            descriptionElement.className = 'productgallery_description';

            descriptionTitleElement = document.createElement('div');
            descriptionTitleElement.className = 'productgallery_description_title';
            descriptionElement.appendChild(descriptionTitleElement);

            descriptionContentElement = document.createElement('div');
            descriptionContentElement.className = 'productgallery_description_content';
            descriptionElement.appendChild(descriptionContentElement);

            self.componentElement.appendChild(descriptionElement);
        }
        // scroll buttons
        if (galleryInfo.galleryImagesList.length > 1) {
            scrollButtonNext = document.createElement('div');
            scrollButtonNext.className = 'productgallery_scrollbutton productgallery_scrollbutton_next scroll_pages_next';
            scrollButtonPrev = document.createElement('div');
            scrollButtonPrev.className = 'productgallery_scrollbutton productgallery_scrollbutton_previous scroll_pages_previous';
            self.componentElement.appendChild(scrollButtonNext);
            self.componentElement.appendChild(scrollButtonPrev);
            self.spNextButton = scrollButtonNext;
            self.spPreviousButton = scrollButtonPrev;
            eventsManager.addHandler(scrollButtonNext, 'click', nextImageHandler);
            eventsManager.addHandler(scrollButtonPrev, 'click', previousImageHandler);
        }

        // scroll container
        if (!(scrollContainer = _('.productgallery_images_container')[0])) {
            scrollContainer = document.createElement('div');
            scrollContainer.className = 'productgallery_images_container';
            self.componentElement.appendChild(scrollContainer);
        }
        scrollContainer.className += ' scroll_pages_container';

        // popup
        if (galleryInfo['popup']) {
            headerGalleryPopup = new HeaderGalleryPopupComponent(galleryInfo, scrollContainer);
            self.componentElement.appendChild(headerGalleryPopup.componentElement);
        }
        // images container
        imagesContainer = document.createElement('div');
        imagesContainer.className = 'productgallery_images scroll_pages_content';
        scrollContainer.appendChild(imagesContainer);

        var imageSwitchersContainer = document.createElement('div');
        imageSwitchersContainer.className = 'productgallery_imageswitchers';
        self.componentElement.appendChild(imageSwitchersContainer);

        // Create images, add switchbuttons for each
        var images = galleryInfo.galleryImagesList;
        var i;
        for (i = 0; i < images.length; i++) {
            var image = new HeaderGalleryImageComponent(images[i], galleryInfo, self);
            imagesContainer.appendChild(image.componentElement);

            galleryImageElements.push(image.componentElement);
            galleryImagesList.push(image);
            galleryImagesIndex[images[i].getId()] = image;
        }
        for (i = 0; i < images.length; i++) {
            imageSwitchers[i] = new ImageSwitcherComponent(self, i);
            imageSwitchersContainer.appendChild(imageSwitchers[i].componentElement);
        }
    };

    var nextImageHandler = function() {
        self.bUserHasInteracted = true;
        self.stopPagesRotation();
        self.showNextPage();
        self.activeImageIndex = self.activeImageIndex == galleryInfo.galleryImagesList.length - 1 ? 0 : self.activeImageIndex + 1;
        controller.fireEvent('headerGalleryImageChanged');
    };
    var previousImageHandler = function() {
        self.bUserHasInteracted = true;
        self.stopPagesRotation();
        self.showPreviousPage();
        self.activeImageIndex = self.activeImageIndex == 0 ? galleryInfo.galleryImagesList.length - 1 : self.activeImageIndex - 1;
        controller.fireEvent('headerGalleryImageChanged');
    };
    var displayComponent = function() {
        componentElement.style.display = 'block';
    };
    var imageChange = function() {
        self.refreshSwitchers(self.activeImageIndex);
    };
    this.refreshSwitchers = function(activeImageIndex) {
        for (var i = 0; i < imageSwitchers.length; i++) {
            imageSwitchers[i].update(activeImageIndex);
        }
    };

    this.init();
};
ScrollPagesMixin.call(ProductGalleryBComponent.prototype);

/**
 *   HEADERGALLERY - IMAGE COMPONENT
 */
window.HeaderGalleryImageComponent = function(image, galleryInfo, gallery) {
    var self = this;
    this.componentElement = null;

    var imageElement;
    var imageDescription;
    var imageDescriptionTitle;
    var imageDescriptionContent;
    var imageLabelElement;

    var galleryImageProductsList = [];

    var imageOriginalWidth;
    var imageOriginalHeight;

    var init = function() {
        createDomStructure();

    };
    var createDomStructure = function() {
        // container
        if (image.getLink()) {
            self.componentElement = document.createElement('a');
            self.componentElement.target = '_blank';
            self.componentElement.href = image.getLink();
        } else {
            self.componentElement = document.createElement('div');
        }
        self.componentElement.className = 'productgallery_image scroll_page';

        // image itself
        imageElement = document.createElement('img');
        self.componentElement.appendChild(imageElement);
        if (galleryInfo.isImageDescriptionEnabled() && (image.getDescription() || image.getTitle())) {
            // description
            imageDescription = document.createElement('div');
            imageDescription.className = 'productgallery_image_description';

            imageDescriptionTitle = document.createElement('div');
            imageDescriptionTitle.className = 'productgallery_image_description_title';
            imageDescriptionTitle.innerHTML = image.getTitle();

            imageDescriptionContent = document.createElement('div');
            imageDescriptionContent.className = 'productgallery_image_description_content';
            imageDescriptionContent.innerHTML = image.getDescription();

            imageDescription.appendChild(imageDescriptionTitle);
            imageDescription.appendChild(imageDescriptionContent);
            self.componentElement.appendChild(imageDescription);
        }

        // label
        if (image.getLabelText() !== '') {
            imageLabelElement = document.createElement('div');
            imageLabelElement.className = 'productgallery_image_label';

            var imageLabelContentElement = document.createElement('div');
            imageLabelContentElement.className = 'productgallery_image_label_content';
            imageLabelContentElement.innerHTML = image.getLabelText();
            imageLabelElement.appendChild(imageLabelContentElement);

            self.componentElement.appendChild(imageLabelElement);
        }
        addPlaceMarks(image.getPlaceMarks());
    };
    var addPlaceMarks = function(placemarks) {
        for (var i = 0; i < placemarks.length; i++) {
            var placeMarkComponent = new HeaderGalleryProductPlacemarkComponent(placemarks[i], gallery);
            galleryImageProductsList.push(placeMarkComponent);
            self.componentElement.appendChild(placeMarkComponent.componentElement);
        }
    };

    this.checkPreloadImage = function(callBack) {
        if (!imageElement.src) {
            imageElement.src = image.getImage();
            imageElement.style.visibility = 'hidden';
            self.componentElement.style.display = '';
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
                self.componentElement.style.display = 'none';
                self.preloaded = true;
                // resizeImageElement();
            }

            if (callBack) {
                callBack();
            }
        }
    };

    init();
};
/**
 *   HEADERGALLERY - PRODUCT PLACEMARK COMPONENT
 */
window.HeaderGalleryProductPlacemarkComponent = function(info, gallery) {
    var self = this;
    this.componentElement = null;

    var placemarkDescriptionElement;
    var placemarkDescriptionTitleElement;
    var placemarkDescriptionContentElement;

    var init = function() {
        createDomStructure();
        eventsManager.addHandler(self.componentElement, 'click', clickHandler);
    };
    var createDomStructure = function() {
        var markerLogic = info.getMarkerLogic();
        self.componentElement = document.createElement('div');
        self.componentElement.className = 'productgallery_productplacemark';
        if (markerLogic != 1) {
            placemarkDescriptionElement = document.createElement('div');
            placemarkDescriptionElement.className = 'productgallery_productplacemark_description';
            self.componentElement.appendChild(placemarkDescriptionElement);
            var products = info.getProducts();
            for (var i = 0; i < products.length; ++i) {
                addProductDetails(products[i]);
            }
        }
        if (markerLogic == 2) {
            self.componentElement.classList.add('productgallery_productplacemark_hoverable');
        }
        self.componentElement.style.left = info.getPositionX() + '%';
        self.componentElement.style.top = info.getPositionY() + '%';
    };
    var addProductDetails = function(productInfo) {
        var productElement = document.createElement('div');
        productElement.className = 'productgallery_productplacemark_product';
        placemarkDescriptionTitleElement = document.createElement('div');
        placemarkDescriptionTitleElement.className = 'productgallery_productplacemark_description_title';
        placemarkDescriptionTitleElement.innerHTML = productInfo.getTitle();
        productElement.appendChild(placemarkDescriptionTitleElement);
        placemarkDescriptionContentElement = document.createElement('div');
        placemarkDescriptionContentElement.className = 'productgallery_productplacemark_description_price';
        placemarkDescriptionContentElement.innerHTML = productInfo.getPrice().toFixed(2) + ' ' + window.selectedCurrencyItem.symbol;
        productElement.appendChild(placemarkDescriptionContentElement);
        placemarkDescriptionElement.appendChild(productElement);
    };
    // open popup with image description
    var clickHandler = function(event) {
        eventsManager.cancelBubbling(event);
        eventsManager.preventDefaultAction(event);
        gallery.selectPlaceMark(info, self);
    };
    this.getComponentElement = function() {
        return self.componentElement;
    };
    init();
};
/**
 *   HEADERGALLERY - PRODUCT POPUP COMPONENT
 */
window.HeaderGalleryPopupComponent = function(galleryInfo, scrollContainer) {
    var self = this;
    this.componentElement = null;
    var contentContainerElement;
    var closeButtonElement;
    var currentPlaceMarkComponent;
    var products = [];

    var init = function() {
        createDomStructure();
        controller.addListener('headerGalleryImageChanged', closeClickHandler);
    };
    var createDomStructure = function() {
        // popup, main container
        self.componentElement = document.createElement('div');
        self.componentElement.className = 'productgallery_popup';
        self.componentElement.style.visibility = 'hidden';

        eventsManager.addHandler(window, 'click', clickOutsideHandler);
        // content (product)
        contentContainerElement = document.createElement('div');
        contentContainerElement.className = 'productgallery_popup_content_container';
        self.componentElement.appendChild(contentContainerElement);
        // close button
        closeButtonElement = document.createElement('div');
        closeButtonElement.className = 'productgallery_popup_close';
        eventsManager.addHandler(closeButtonElement, 'click', closeClickHandler);

        self.componentElement.appendChild(closeButtonElement);

    };
    var clickOutsideHandler = function(event) {
        if (self.componentElement.contains(event.target)
            || event.target.className == 'productgallery_productplacemark'
            || event.target.className == 'productgallery_productplacemark_description_title') {
            return false;
        } else {
            closeClickHandler();
        }
    };
    var closeClickHandler = function() {
        hideComponent();
    };
    var setProductsInfo = function(productsInfo) {
        if (self.componentElement.style.visibility == 'visible') {
            hideComponent();
        }
        while (contentContainerElement.firstChild) {
            contentContainerElement.removeChild(contentContainerElement.firstChild);
        }
        products.length = 0;
        for (var i = 0; i < productsInfo.length; ++i) {
            var product = new HeaderGalleryPopupProductComponent(productsInfo[i]);
            contentContainerElement.appendChild(product.getComponentElement());
            products.push(product);
        }
        displayComponent(preload);
    };
    this.setCurrentPlaceMarkComponent = function(newPlaceMarkComponent, products) {
        if (currentPlaceMarkComponent != newPlaceMarkComponent) {
            currentPlaceMarkComponent = newPlaceMarkComponent;
            setProductsInfo(products);
        }
    };
    var preload = function() {
        for (var i = products.length; i--;) {
            if (!products[i].isImageComplete()) {
                setTimeout(preload, 100);
                return;
            }
        }
    };
    var displayComponent = function(callback) {
        self.componentElement.style.opacity = 0;
        self.componentElement.style.visibility = 'visible';
        var leftPosition, topPosition;
        if (galleryInfo.getPopupPositioning() == 'mark' && currentPlaceMarkComponent) {
            var placeMarkElement = currentPlaceMarkComponent.getComponentElement();
            if (placeMarkElement) {
                if (placeMarkElement.offsetTop > placeMarkElement.parentNode.offsetHeight / 2) {
                    topPosition = placeMarkElement.offsetTop - self.componentElement.offsetHeight;
                } else {
                    topPosition = placeMarkElement.offsetTop + placeMarkElement.offsetHeight;
                }
                if (placeMarkElement.offsetLeft > placeMarkElement.parentNode.offsetWidth / 2) {
                    leftPosition = placeMarkElement.offsetLeft - self.componentElement.offsetWidth + scrollContainer.offsetLeft;
                } else {
                    leftPosition = placeMarkElement.offsetLeft + placeMarkElement.offsetWidth + scrollContainer.offsetLeft;
                }

            }

        } else {
            leftPosition = self.componentElement.parentNode.offsetWidth / 2 - self.componentElement.offsetWidth / 2;
            topPosition = self.componentElement.parentNode.offsetHeight / 2 - self.componentElement.offsetHeight / 2;
        }

        self.componentElement.style.left = leftPosition + 'px';
        self.componentElement.style.top = topPosition + 'px';
        TweenLite.to(self.componentElement, 0.2, {
            'ease': 'Power2.easeInOut',
            'css': {'opacity': 1},
            'onComplete': callback,
        });
    };
    var hideComponent = function() {
        TweenLite.to(self.componentElement, 0.2, {
            'css': {'opacity': 0},
            'ease': 'Power2.easeInOut',
            'onComplete': finishHideComponent,
        });
    };
    var finishHideComponent = function() {
        self.componentElement.style.visibility = 'hidden';
    };

    init();
};

window.HeaderGalleryPopupProductComponent = function(productInfo) {
    var componentElement, imageElement;

    var init = function() {
        componentElement = document.createElement('div');
        componentElement.className = 'productgallery_popup_product';

        // image
        var imageContainerElement = document.createElement('div');
        imageContainerElement.className = 'productgallery_popup_product_image_container';
        imageElement = document.createElement('img');
        imageElement.className = 'productgallery_popup_product_image';
        imageContainerElement.appendChild(imageElement);

        var contentContainerElement = document.createElement('div');
        contentContainerElement.className = 'productgallery_popup_product_content_container';

        var contentTitleElement = document.createElement('div');
        contentTitleElement.className = 'productgallery_popup_product_title';
        contentContainerElement.appendChild(contentTitleElement);

        var contentPriceElement = document.createElement('div');
        contentPriceElement.className = 'productgallery_popup_product_price';

        var element = document.createElement('span');
        element.innerHTML = window.translationsLogics.get('productgallery.price') + ': ';
        contentPriceElement.appendChild(element);

        var contentPriceValueElement = document.createElement('span');
        contentPriceValueElement.className = 'productgallery_popup_product_price_value';
        contentPriceElement.appendChild(contentPriceValueElement);

        var contentTextElement = document.createElement('div');
        contentTextElement.className = 'productgallery_popup_product_description html_content';

        var parametersTextElement = document.createElement('div');
        parametersTextElement.className = 'productgallery_popup_product_parameters';

        var contentLinkElement = document.createElement('a');
        contentLinkElement.className = 'button productgallery_popup_product_link';
        var contentLinkTextElement = document.createElement('span');
        contentLinkTextElement.className = 'button_text productgallery_popup_product_link_text';
        contentLinkTextElement.innerHTML = window.translationsLogics.get('productgallery.readmore');
        contentLinkElement.appendChild(contentLinkTextElement);

        var image = productInfo.getImage();
        if (image != '') {
            imageElement.src = productInfo.getImage();
            imageContainerElement.style.display = 'inline-block';
        } else {
            imageContainerElement.style.display = 'none';
        }

        contentTitleElement.innerHTML = productInfo.getTitle();
        contentTextElement.innerHTML = productInfo.getDescription();
        contentPriceValueElement.innerHTML = productInfo.getPrice().toFixed(2) + ' ' + window.selectedCurrencyItem.symbol;
        contentLinkElement.href = productInfo.getUrl();

        var primaryParametersInfo = productInfo.getPrimaryParametersInfo();

        if (primaryParametersInfo) {
            for (var i = 0; i < primaryParametersInfo.length; i++) {
                var parameterRow = document.createElement('div');
                parameterRow.className = 'productgallery_popup_product_parameter';
                if (primaryParametersInfo[i].title != '') {
                    var titleElement = document.createElement('span');
                    titleElement.innerHTML = primaryParametersInfo[i].title + ': ';
                    titleElement.className = 'productgallery_popup_product_parameter_title';
                    parameterRow.appendChild(titleElement);
                }
                var valueElement = document.createElement('span');
                valueElement.className = 'productgallery_popup_product_parameter_value';
                if (primaryParametersInfo[i].structureType === 'productParameter') {
                    valueElement.innerHTML = primaryParametersInfo[i].value;
                } else if (primaryParametersInfo[i].structureType === 'productSelection') {
                    for (var j = 0; j < primaryParametersInfo[i].productOptions.length; j++) {
                        if (primaryParametersInfo[i].productOptions[j] !== null) {
                            if (primaryParametersInfo[i].type === 'color') {
                                var valueOptionElement = document.createElement('div');
                                valueOptionElement.className = 'productgallery_popup_product_parameter_value_option';
                                valueOptionElement.style.display = 'inline-block';
                                valueOptionElement.style.border = '1px solid #ececec';
                                valueOptionElement.style.borderRadius = '10%';
                                valueOptionElement.style.backgroundColor = '#' + primaryParametersInfo[i].productOptions[j].value;
                                valueOptionElement.style.width = '1em';
                                valueOptionElement.style.height = '1em';
                                valueElement.appendChild(valueOptionElement);
                            } else {
                                var valueOptionElement = document.createElement('span');
                                valueOptionElement.className = 'productgallery_popup_product_parameter_value_option';
                                valueOptionElement.innerHTML = primaryParametersInfo[i].productOptions[j].title;
                                valueElement.appendChild(valueOptionElement);
                            }
                        }
                    }
                }
                parameterRow.appendChild(valueElement);
                parametersTextElement.appendChild(parameterRow);
            }
        }

        componentElement.appendChild(imageContainerElement);
        componentElement.appendChild(contentContainerElement);

        contentContainerElement.appendChild(contentPriceElement);
        contentContainerElement.appendChild(contentTextElement);
        contentContainerElement.appendChild(parametersTextElement);
        contentContainerElement.appendChild(contentLinkElement);
    };
    this.isImageComplete = function() {
        return imageElement.complete;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};

/**
 *   HEADERGALLERY - IMAGE SWITCHBUTTON
 */
window.ImageSwitcherComponent = function(galleryComponent, index) {
    var self = this;
    this.componentElement = null;
    this.index = index;

    var init = function() {
        createDomStructure();
        eventsManager.addHandler(self.componentElement, 'click', clickHandler);
    };
    var createDomStructure = function() {
        self.componentElement = document.createElement('div');
        if (index != 0) {
            self.componentElement.className = 'productgallery_imageswitcher';
        } else {
            self.componentElement.className = 'productgallery_imageswitcher productgallery_imageswitcher_active';
        }
    };
    var clickHandler = function() {
        galleryComponent.stopPagesRotation();
        galleryComponent.showPage(index);
        galleryComponent.refreshSwitchers(index);
    };
    this.update = function(activeIdex) {
        if (this.index != activeIdex) {
            window.domHelper.removeClass(self.componentElement, 'productgallery_imageswitcher_active');
        } else {
            window.domHelper.addClass(self.componentElement, 'productgallery_imageswitcher_active');
        }
    };
    init();
};