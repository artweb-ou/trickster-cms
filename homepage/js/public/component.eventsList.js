window.EventsListComponent = function(componentElement) {
    var self = this;
    var presetInputs = [];
    var periodSelector;

    var init = function() {
        var element;
        if (element = componentElement.querySelector('select.eventslist_filter_select')) {
            periodSelector = new EventsListSelectorComponent(element, self);
        }
        var elements = componentElement.querySelectorAll('input.eventslist_filter_preset_radio');
        for (var i = 0; i < elements.length; i++) {
            presetInputs.push(new EventsListRadioComponent(elements[i], self));
        }
    };
    this.applyFilters = function() {
        var url = window.currentElementURL;
        var filterValue;
        for (var i = 0; i < presetInputs.length; i++) {
            if (filterValue = presetInputs[i].getValue()) {
                url += 'preset:' + filterValue + '/';
            }
        }
        if (!filterValue && periodSelector) {
            if (filterValue = periodSelector.getValue()) {
                if (filterValue != 'none') {
                    url += 'period:' + periodSelector.getValue() + '/';
                }
            }
        }
        document.location.href = url;
    };
    init();
};

window.EventsListSelectorComponent = function(componentElement, eventsListObject) {
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

window.EventsListRadioComponent = function(componentElement, eventsListObject) {
    var self = this;
    var initialValue;

    var init = function() {
        initialValue = self.getValue();
        eventsManager.addHandler(componentElement, 'click', changeHandler);
    };
    var changeHandler = function() {
        if (self.getValue() != initialValue) {
            eventsListObject.applyFilters();
        }
    };
    this.getValue = function() {
        if (componentElement.checked) {
            return componentElement.value;
        } else {
            return false;
        }
    };
    init();
};