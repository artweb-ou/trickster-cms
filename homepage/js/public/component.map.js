window.MapComponent = function(componentElement, id) {
    var self = this;
    var info;
    var infowindow, marker, googleMap, latlng;

    var init = function() {
        self.initLazyLoading({
            'componentElement': componentElement,
            'displayCallback': lazyLoadingCallback,
        });
    };
    var lazyLoadingCallback = function() {
        info = window.mapsLogics.getMapInfo(id);
        eventsManager.addHandler(window, 'resize', onResize);

        if (info.isHeightAdjusted()) {
            adjustHeight();
        }

        var mapCode = info.getMapCode();
        if (mapCode) {
            componentElement.innerHTML = mapCode;
        } else {
            var coordinates = info.getCoordinates();
            if (coordinates) {
                var styles = info.getStyles();
                if (styles) {
                    latlng = new google.maps.LatLng(coordinates[0], coordinates[1]);
                    var options = {
                        zoom: 14,
                        //disableDefaultUI: true,
                        center: latlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        mapTypeControl: info.getMapTypeControlEnabled(),
                        zoomControl: info.getZoomControlEnabled(),
                        streetViewControl: info.getStreetViewControlEnabled(),
                    };
                    if (styles.length > 0) {
                        options.styles = styles;
                    }
                    googleMap = new google.maps.Map(componentElement, options);

                    if (info.getContent()) {
                        infowindow = new google.maps.InfoWindow({
                            content: info.getContent(),
                        });
                    }
                    marker = new google.maps.Marker({
                        position: latlng,
                        map: googleMap,
                        title: info.getTitle(),
                    });
                    google.maps.event.addListener(marker, 'click', onMarkerClick);
                } else {
                    // var title = info.getTitle() ? '&q=+(' + info.getTitle() + ')' : '';
                    var title = info.getTitle() ? '&q=+' + info.getTitle() + '' : '';

                    var iframe = document.createElement('iframe');
                    componentElement.classList.add('googlemap_iframe');
                    // var src = 'https://maps.google.com/?q=' + coordinates + title + '&hl=' + window.mapsLogics.getShortLanguageCode() + '&z=' + 14 + '&output=embed';
                    var src = 'https://maps.google.com/?q=' + title + '&ll='+ coordinates + '&hl=' + window.mapsLogics.getShortLanguageCode() + '&z=' + 14 + '&output=embed';
                    iframe.setAttribute('src', src);
                    iframe.setAttribute('allowfullscreen', true);
                    componentElement.appendChild(iframe);
                }
            }
        }
    };

    var onMarkerClick = function() {
        infowindow.open(googleMap, marker);
    };

    var onResize = function() {
        if (info.isHeightAdjusted()) {
            adjustHeight();
        }
        refreshGoogleMap();
    };

    var adjustHeight = function() {
        var componentHeight;
        componentElement.style.height = '';

        var parentHeight = componentElement.parentNode.offsetHeight;
        var minHeight = componentElement.offsetWidth * info.getHeight();
        if (minHeight > parentHeight) {
            componentHeight = minHeight;
        } else {
            componentHeight = parentHeight;
        }
        componentElement.style.height = componentHeight + 'px';
    };

    var refreshGoogleMap = function() {
        if (googleMap) {
            google.maps.event.trigger(googleMap, 'resize');
            googleMap.setCenter(latlng);
        }
    };
    init();
};
LazyLoadingMixin.call(MapComponent.prototype);

window.iframeMapComponent = function(componentElement, id, lang) {
    var info;
    var init = function() {
        adjustHeight();
        eventsManager.addHandler(window, 'resize', adjustHeight);
    };

    var adjustHeight = function() {
        info = window.mapsLogics.getMapInfo(id);
        var coordinates = info.getCoordinates();

        componentElement.style.minHeight = componentElement.offsetWidth / 2 + 'px';
        componentElement.style.height = '';
        componentElement.style.height = componentElement.parentNode.offsetHeight + 'px';
    };
    init();
};