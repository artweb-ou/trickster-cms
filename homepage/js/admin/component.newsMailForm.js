window.NewsMailForm = function(componentElement) {
    var addressSelectElement;
    var groupSelectionElement;
    var groupCheckboxElements;

    var init = function() {
        if (addressSelectElement = _('select.newsmailtext_address_select', componentElement)[0]) {
            new AjaxSelectComponent(addressSelectElement, 'newsMailAddress', 'admin');
        }

        groupSelectionElement = _('.newsmailstext_group_checkbox', componentElement)[0];
        groupCheckboxElements = _('.newsmailstext_group', componentElement);
        eventsManager.addHandler(groupSelectionElement, 'click', checkGroupSelection);
    };
    var checkGroupSelection = function() {
        var selectionStatus;
        var className = 'checked';
        selectionStatus = groupSelectionElement.classList.contains(className);
        for (var i = groupCheckboxElements.length; i--;) {
            if (selectionStatus) {
                groupCheckboxElements[i].classList.add(className);
            } else {
                groupCheckboxElements[i].classList.remove(className);
            }
        }
    };
    init();
};