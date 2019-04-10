window.FormSelectOptionFormComponent = function(componentElement) {
    var connectedSelectElements;

    var init = function() {
        connectedFieldsElements = _('.connectedfields_select', componentElement);
        for (var i = connectedFieldsElements.length; i--;) {
            new AjaxSelectComponent(connectedFieldsElements[i], 'formField', 'admin');
        }
    };

    init();
};