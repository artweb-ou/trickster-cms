window.PagerComponent = function(componentElement, pagerData) {
    var self = this;
    var init = function() {
        var button;
        if (!componentElement) {
            componentElement = document.createElement('div');
            componentElement.className = 'pager_block';
        } else {
            while (componentElement.firstChild) {
                componentElement.removeChild(componentElement.firstChild);
            }
        }

        button = new PagerPreviousComponent(pagerData.previousPage);
        componentElement.appendChild(button.getComponentElement());

        for (var i = 0; i < pagerData.pagesList.length; i++) {
            var pageData = pagerData.pagesList[i];
            var page = new PagerPageComponent(pageData);
            componentElement.appendChild(page.getComponentElement());
        }
        button = new PagerNextComponent(pagerData.nextPage);
        componentElement.appendChild(button.getComponentElement());
    };
    this.getComponentElement = function() {
        return componentElement;
    };

    init();
};
window.PagerPageComponent = function(data) {
    var componentElement;
    var self = this;
    var init = function() {
        if (data.active) {
            componentElement = document.createElement('a');
            componentElement.href = data.URL;
        } else {
            componentElement = document.createElement('span');
        }
        componentElement.className = 'pager_page';
        if (data.selected) {
            componentElement.className += ' pager_active';
        }
        componentElement.innerHTML = data.text;
        if (data.active) {
            eventsManager.addHandler(componentElement, 'click', click);
        }
    };
    var click = function(event) {
        eventsManager.preventDefaultAction(event);
        window.urlParameters.setParameter('page', data.number);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(PagerPageComponent.prototype);
window.PagerPreviousComponent = function(data) {
    var componentElement;
    var self = this;
    var init = function() {
        componentElement = document.createElement('a');
        componentElement.className = 'pager_previous';
        if (data.active) {
            componentElement.href = data.URL;
        } else {
            componentElement.href = '';
            componentElement.className += ' pager_hidden';
        }
        componentElement.innerHTML = data.text;
        if (data.active) {
            eventsManager.addHandler(componentElement, 'click', click);
        }
    };
    var click = function(event) {
        eventsManager.preventDefaultAction(event);
        window.urlParameters.setParameter('page', data.number);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
window.PagerNextComponent = function(data) {
    var componentElement;
    var self = this;
    var init = function() {
        componentElement = document.createElement('a');
        componentElement.className = 'pager_next';
        if (data.active) {
            componentElement.href = data.URL;
        } else {
            componentElement.href = '';
            componentElement.className += ' pager_hidden';
        }
        componentElement.innerHTML = data.text;
        if (data.active) {
            eventsManager.addHandler(componentElement, 'click', click);
        }
    };
    var click = function(event) {
        eventsManager.preventDefaultAction(event);
        window.urlParameters.setParameter('page', data.number);
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
