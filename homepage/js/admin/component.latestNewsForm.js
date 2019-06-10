window.LatestNewsFormComponent = function(componentElement) {
    var self = this;
    var typeSelector = false;
    var autoRowElements, manualRowElements = false;
    var connectedNewsSelectElement = false;
    var newsListSelectElement = false;

    var init = function() {
        if (typeSelector = _('select.latest_news_modify_type', componentElement)[0]) {
            eventsManager.addHandler(typeSelector, 'change', changeHandler);
            autoRowElements = _('.latest_news_modify_auto', componentElement);
            manualRowElements = _('.latest_news_modify_manual', componentElement);
        }
        if (connectedNewsSelectElement = _('.latest_news_connected_select', componentElement)[0]) {
            new AjaxSelectComponent(connectedNewsSelectElement, 'news', 'admin');
        }
        if (newsListSelectElement = _('.latest_news_newlist_select', componentElement)[0]) {
            new AjaxSelectComponent(newsListSelectElement, 'newsList', 'admin');
        }
        refreshState();
    };
    var changeHandler = function() {
        refreshState();
    };
    var refreshState = function() {
        if (typeSelector.value == 'auto') {
            for (var i = manualRowElements.length; i--;) {
                domHelper.addClass(manualRowElements[i], 'hidden');

            }
            for (var i = autoRowElements.length; i--;) {
                domHelper.removeClass(autoRowElements[i], 'hidden');
            }
        }
        if (typeSelector.value == 'manual') {
            for (var i = manualRowElements.length; i--;) {
                domHelper.removeClass(manualRowElements[i], 'hidden');

            }
            for (var i = autoRowElements.length; i--;) {
                domHelper.addClass(autoRowElements[i], 'hidden');
            }
        }
    };

    init();
};