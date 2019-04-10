window.MapComponent = function (componentElement, id) {
  var self = this
  var info
  var infowindow, marker, googleMap, latlng

  var init = function () {
    self.initLazyLoading({
      'componentElement': componentElement,
      'displayCallback': lazyLoadingCallback
    })
  }
  var lazyLoadingCallback = function () {
    info = window.mapsLogics.mapsIndex[id]
    eventsManager.addHandler(window, 'resize', onResize)

    if (info.isHeightAdjusted()) {
      adjustHeight()
    }

    var coordinates = info.getCoordinates()
    latlng = new google.maps.LatLng(coordinates[0], coordinates[1])
    var options = {
      zoom: 14,
      //disableDefaultUI: true,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: info.getMapTypeControlEnabled(),
      zoomControl: info.getZoomControlEnabled(),
      streetViewControl: info.getStreetViewControlEnabled()
    }
    var styles = info.getStyles()
    if (styles.length > 0) {
      options.styles = styles
    }
    googleMap = new google.maps.Map(componentElement, options)

    if (info.getContent()) {
      infowindow = new google.maps.InfoWindow({

        content: info.getContent()
      })
    }
    marker = new google.maps.Marker({
      position: latlng,
      map: googleMap,
      title: info.getTitle()
    })
    google.maps.event.addListener(marker, 'click', onMarkerClick)
  }

  var onMarkerClick = function () {
    infowindow.open(googleMap, marker)
  }

  var onResize = function () {
    if (info.isHeightAdjusted()) {
      adjustHeight()
    }
    refreshGoogleMap()
  }

  var adjustHeight = function () {
    var componentHeight
    componentElement.style.height = ''

    var parentHeight = componentElement.parentNode.offsetHeight
    var minHeight = componentElement.offsetWidth * info.getHeight()
    if (minHeight > parentHeight) {
      componentHeight = minHeight
    } else {
      componentHeight = parentHeight
    }
    componentElement.style.height = componentHeight + 'px'
  }

  var refreshGoogleMap = function () {
    if (googleMap) {
      google.maps.event.trigger(googleMap, 'resize')
      googleMap.setCenter(latlng)
    }
  }
  init()
}
LazyLoadingMixin.call(MapComponent.prototype)

window.EmbeddedMapComponent = function (componentElement) {
  var init = function () {
    adjustHeight()
    eventsManager.addHandler(window, 'resize', adjustHeight)
  }

  var adjustHeight = function () {
    componentElement.style.minHeight = componentElement.offsetWidth / 2 + 'px'
    componentElement.style.height = ''
    componentElement.style.height = componentElement.parentNode.offsetHeight + 'px'
  }
  init()
}