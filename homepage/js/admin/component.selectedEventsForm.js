window.SelectedEventsFormComponent = function(componentElement) {
    var self = this;
    var searchInputEl;

    var init = function() {
        createDomStructure();
        var types = searchInputEl.getAttribute('data-types');
        var apiMode = 'admin';

        new AjaxSelectComponent(searchInputEl, types, apiMode);
    };

    var createDomStructure = function() {
        searchInputEl = _('.selectedevents_form_search', componentElement)[0];
    };

    init();
};
