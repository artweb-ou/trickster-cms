window.commentFormLogics = new function() {
    var initComponents = function() {
        var elements = _('.comment_form');
        for (var i = 0; i < elements.length; i++) {
            new CommentFormComponent(elements[i]);
        }
    };
    controller.addListener('initDom', initComponents);
};