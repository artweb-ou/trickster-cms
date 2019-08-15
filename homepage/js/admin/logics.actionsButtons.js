window.actionsButtonsLogics = new function() {
    var controlsElements;
    var containerElements;
    var initComponents = function() {
        controlsElements = document.querySelector('.controls_block');
        containerElements = document.querySelector('.content_list');
        if (controlsElements && containerElements) {
            new ActionsButtonsComponent(controlsElements, containerElements);
        }
    };
    controller.addListener('initDom', initComponents);
};