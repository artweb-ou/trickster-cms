window.lazyImageLogics = new function () {
  var initComponents = function () {
    var elements = _('.lazy_image')
    for (var i = elements.length; i--;) {
      new LazyImageComponent(elements[i])
    }
  }
  controller.addListener('startApplication', initComponents)
}