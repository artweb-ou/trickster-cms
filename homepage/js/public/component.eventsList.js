window.EventsListComponent = function(componentElement) {
    var self = this;
    var filterer;

    var init = function() {
        var element;
        if (element = componentElement.querySelector('select.eventslist_filter_select')) {
            filterer = new EventsListFilterComponent(element, self);
        }
    };
    this.applyFilters = function() {
        var url = window.currentElementURL;
        if (filterer) {
            var filterValue = filterer.getValue();
            if (filterValue) {
                if (filterValue != 'none') {
                    url += 'period:' + filterer.getValue() + '/';
                }
                document.location.href = url;
            }
        }
    };
    init();
};

window.EventsListFilterComponent = function(componentElement, eventsListObject) {
    var self = this;
    var initialValue;

    var init = function() {
        initialValue = self.getValue();
        eventsManager.addHandler(componentElement, 'change', changeHandler);
    };
    var changeHandler = function() {
        if (self.getValue() != initialValue) {
            eventsListObject.applyFilters();
        }
    };
    this.getValue = function() {
        return componentElement.value;
    };
    init();
};