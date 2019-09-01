window.autocompleteOptionsComponent = function(formElement, autocompleteOptionsElement) {
    var hide_public;
    var hiddenRows;
    var hiddenRowSelector= '.row_if_autocomplete';
    var isPageUrl;
    var i;

    var init = function() {
            eventsManager.addHandler(autocompleteOptionsElement, 'change', checkDisplay);
            checkDisplay();
    };

    var checkDisplay = function() {
        hide_public = formElement.querySelector('.hide_public.checkbox_placeholder');
        hiddenRows = formElement.querySelectorAll(hiddenRowSelector);
        isPageUrl = autocompleteOptionsElement.value === 'pageUrl';
        if (hide_public) {
            if(isPageUrl) {
                hide_public.checked = true;
                eventsManager.fireEvent(hide_public, 'change');
            }
            if (hiddenRows.length>0) {
                for (i = hiddenRows.length; i--;) {
                    hiddenRows[i].style.display = isPageUrl ? 'none' : '';
                }
            }
        }
    };

    init();
};