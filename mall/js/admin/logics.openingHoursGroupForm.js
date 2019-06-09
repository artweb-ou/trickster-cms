window.openingHoursGroupFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.openinghours_group_form');
        for (var i = elements.length; i--;) {
            new OpeningHoursGroupFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};