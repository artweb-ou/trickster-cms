window.mapsLogics = new function() {
    var self = this;
    var mapsIndex = {};

    var initLogics = function() {
        if (typeof window.mapsInfo !== 'undefined') {
            for (var id in mapsInfo) {
                if (mapsInfo.hasOwnProperty(id)) {
                    mapsIndex[id] = new MapInfo(mapsInfo[id]);
                }
            }
            controller.addListener('initDom', initDom);
        }
    };

    var initDom = function() {
        var googleApiRequired = false;
        for (var id in mapsIndex) {
            if (mapsIndex.hasOwnProperty(id)) {
                if (mapsIndex[id].getStyles()) {
                    googleApiRequired = true;
                    break;
                }
            }
        }
        if (googleApiRequired) {
            injectGoogleMapsApi();
        } else {
            self.initGoogleMapComponents();
        }

        var embeddedMapElements = _('.map_embedded');
        for (var i = embeddedMapElements.length; i--;) {
            new EmbeddedMapComponent(embeddedMapElements[i]);
        }
    };
    this.getMapInfo = function(id) {
        if (typeof mapsIndex[id] !== 'undefined') {
            return mapsIndex[id];
        }
        return false;
    };
    /**
     * Adds maps api js into document. Necessary with non HTML5 doc
     */
    var injectGoogleMapsApi = function() {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '//maps.googleapis.com/maps/api/js?language=' + self.getShortLanguageCode() + '&callback=window.mapsLogics.initGoogleMapComponents&key=AIzaSyD6IK7At5KLa_vYFFxGcE6ml9VC2WQTWHw';
        document.body.appendChild(script);
    };

    this.initGoogleMapComponents = function() {
        for (var id in mapsIndex) {
            var elements = _('.googlemap_id_' + id);
            for (var i = elements.length; i--;) {
                new MapComponent(elements[i], id);
            }
        }
    };

    /**
     * Active CMS language code (ISO 639‑3) to ISO 639‑2
     * @return {String}
     */
    this.getShortLanguageCode = function() {
        var result = 'en';
        if (window.currentLanguageCode) {
            switch (window.currentLanguageCode) {
                case 'est':
                    result = 'et';
                    break;
                case 'rus':
                    result = 'ru';
                    break;
                case 'fin':
                    result = 'fi';
                    break;
                default:
                    break;
            }
        }
        return result;
    };
    controller.addListener('initLogics', initLogics);
};

window.MapInfo = function(info) {
    var coordinates = [];
    var styles;
    var mapCode;
    var title = '';
    var content = '';
    var heightAdjusted = false;
    var mapTypeControlEnabled = false;
    var zoomControlEnabled = false;
    var streetViewControlEnabled = false;
    var height;

    var init = function() {
        coordinates = info.coordinates.replace('"', '').split(',');
        title = info.title;
        mapCode = info.mapCode;
        heightAdjusted = info.heightAdjusted;
        styles = info.styles ? info.styles : false;
        if (typeof info.height != 'undefined') {
            height = info.height;
        } else {
            height = 0.5;
        }
        mapTypeControlEnabled = !!info.mapTypeControlEnabled;
        zoomControlEnabled = !!info.zoomControlEnabled;
        streetViewControlEnabled = !!info.streetViewControlEnabled;
        content = info.content;
    };
    this.getId = function() {
        return id;
    };
    this.getCoordinates = function() {
        return coordinates;
    };
    this.getTitle = function() {
        return title;
    };
    this.getContent = function() {
        return content;
    };
    this.isHeightAdjusted = function() {
        return heightAdjusted;
    };
    this.getHeight = function() {
        return height;
    };
    this.getMapCode = function() {
        return mapCode;
    };
    this.getStyles = function() {
        return styles;
    };
    this.getMapTypeControlEnabled = function() {
        return mapTypeControlEnabled;
    };
    this.getZoomControlEnabled = function() {
        return zoomControlEnabled;
    };
    this.getStreetViewControlEnabled = function() {
        return streetViewControlEnabled;
    };
    init();
};