window.ActionsButtonsComponent = function(controlsElement, containerElement) {
    var hinted;
    var hint;
    var hintClassName = 'action_hint';
    var actionsButtons;
    var contentListElements;
    var contentListCheckedElements;
    var i;

    this.init = function() {
        if (containerElement.querySelectorAll('.singlebox').length > 0 && controlsElement.querySelectorAll('.actions_form_button').length > 0) {
            actionsButtons = controlsElement.querySelectorAll('.actions_form_button');
            contentListElements = containerElement.querySelectorAll('.singlebox');
            for (i = contentListElements.length; i--;) {
                eventsManager.addHandler(contentListElements[i], 'change', isEnableActionsButtons);
            }
        }
    };

    var isEnableActionsButtons = function() {
        contentListCheckedElements = containerElement.querySelectorAll('.singlebox:checked');
        if (contentListCheckedElements.length > 0) {
            for (i = actionsButtons.length; i--;) {
                actionsButtons[i].disabled = false;
            }
        }

        else {
            for (i = actionsButtons.length; i--;) {
                actionsButtons[i].disabled = true;
            }
        }
    };

    var popup_hint = function() {
        for (i = actionsButtons.length; i--;) {
            hinted = actionsButtons[i];
            hint = document.createElement('span');
            hint.classList.add(hintClassName);
            hint.innerHTML = translationsLogics.get('message.any_must_be_checked');
            hinted.appendChild(hint);
        }
    };

    this.init();
    isEnableActionsButtons();
    popup_hint();
};