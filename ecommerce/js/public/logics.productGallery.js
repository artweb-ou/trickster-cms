window.productGalleryLogics = new function() {
    var self = this;
    var galleriesInfoIndex = {};
    var galleriesIndex = {};
    self.currentImageId = false;

    var initData = function() {
        if (typeof window.productGalleriesInfo != 'undefined') {
            importData(window.productGalleriesInfo);
        }
    };
    var importData = function(galleriesData) {
        for (var id in galleriesData) {
            galleriesInfoIndex[id] = new ProductGalleryInfo(galleriesData[id]);
        }
    };
    var initComponents = function() {
        for (var id in galleriesInfoIndex) {
            var elements = _('.productgallery_id_' + id);
            for (var i = 0; i < elements.length; i++) {
                galleriesIndex[id] = new ProductGalleryBComponent(elements[i], galleriesInfoIndex[id]);
            }
        }
    };
    controller.addListener('initLogics', initData);
    controller.addListener('initDom', initComponents);
};

window.ProductGalleryInfo = function(galleryData) {
    var self = this;

    this.galleryImagesList = [];
    this.galleryImagesIndex = {};

    this.id = false;
    this.galleryTitle = false;
    this.galleryDescription = false;
    var popupPositioning = 'center';
    var staticDescriptionEnabled = false;
    var imageDescriptionEnabled = true;
    var heightLogics = 'containerHeight';
    var height = false;

    var init = function() {
        self.id = galleryData.id;
        self.galleryTitle = galleryData.title;
        self.galleryDescription = galleryData.description;
        self.popup = galleryData.popup;
        popupPositioning = galleryData.popupPositioning;
        staticDescriptionEnabled = galleryData.staticDescriptionEnabled;
        imageDescriptionEnabled = galleryData.imageDescriptionEnabled;
        self.markerLogic = galleryData.markerLogic;
        if (typeof galleryData.heightLogics != 'undefined') {
            heightLogics = galleryData.heightLogics;
        }
        if (typeof galleryData.height != 'undefined') {
            height = galleryData.height;
        }

        for (var i = 0; i < galleryData.images.length; i++) {
            var galleryImage = new HeaderGalleryImage();
            galleryImage.updateData(galleryData.images[i]);

            self.galleryImagesList.push(galleryImage);
            self.galleryImagesIndex[galleryImage.getId()] = galleryImage;
        }
    };
    this.getPopupPositioning = function() {
        return popupPositioning;
    };
    this.getHeightLogics = function() {
        return heightLogics;
    };
    this.getHeight = function() {
        return height;
    };
    this.isStaticDescriptionEnabled = function() {
        return staticDescriptionEnabled;
    };
    this.isImageDescriptionEnabled = function() {
        return imageDescriptionEnabled;
    };
    init();
};

window.HeaderGalleryImage = function() {
    var self = this;

    var placeMarks = [];

    var id;
    var image;
    var title;
    var description;
    var labelText;
    var link;

    this.updateData = function(data) {
        id = parseInt(data.id, 10);
        title = data.title;
        description = data.description;
        image = data.image;
        labelText = data.labelText;
        link = data.link;
        for (var i = 0; i < data.placeMarks.length; i++) {
            if (data.placeMarks[i].markerLogic != 0) {
                var product = new HeaderGalleryImagePlacemark();
                product.updateData(data.placeMarks[i]);
                placeMarks.push(product);
            }
        }
    };

    this.getId = function() {
        return id;
    };
    this.getLabelText = function() {
        return labelText;
    };
    this.getImage = function() {
        return image;
    };
    this.getTitle = function() {
        return title;
    };
    this.getDescription = function() {
        return description;
    };
    this.getLink = function() {
        return link;
    };
    this.getPlaceMarks = function() {
        return placeMarks;
    };
};
window.HeaderGalleryImagePlacemark = function() {
    var self = this;
    var positionX;
    var positionY;
    var products = [];
    var productsIndex = {};
    var markerLogic;
    this.updateData = function(data) {
        markerLogic = data.markerLogic;
        positionX = parseFloat(data.positionX, 10);
        positionY = parseFloat(data.positionY, 10);
        for (var i = 0; i < data.products.length; i++) {
            data.products[i].markerLogic = data.markerLogic;
            var product = new HeaderGalleryImageProduct();
            product.updateData(data.products[i]);
            products.push(product);
            productsIndex[product.getId()] = product;
        }
    };
    this.getPositionX = function() {
        return positionX;
    };
    this.getPositionY = function() {
        return positionY;
    };
    this.getProducts = function() {
        return products;
    };
    this.getMarkerLogic = function() {
        return markerLogic;
    };
    this.getProductInfoById = function(productId) {
        var result = false;
        if (typeof productsIndex[productId] != 'undefined') {
            result = productsIndex[productId];
        }
        return result;
    };
};
window.HeaderGalleryImageProduct = function() {
    var self = this;

    var id;
    var positionX;
    var positionY;
    var title;
    var description;
    var price;
    var image;
    var url;
    var primaryParametersInfo;
    var markerLogic;

    this.updateData = function(data) {
        id = parseInt(data.id, 10);
        positionX = parseFloat(data.positionX, 10);
        positionY = parseFloat(data.positionY, 10);
        title = data.title;
        description = data.description;
        price = parseFloat(data.price, 10);
        image = data.image;
        url = data.url;
        primaryParametersInfo = data.primaryParametersInfo;
    };

    this.getId = function() {
        return id;
    };
    this.getTitle = function() {
        return title;
    };
    this.getDescription = function() {
        return description;
    };
    this.getPrice = function() {
        return price;
    };
    this.getImage = function() {
        return image;
    };
    this.getUrl = function() {
        return url;
    };
    this.getPrimaryParametersInfo = function() {
        return primaryParametersInfo;
    };

};