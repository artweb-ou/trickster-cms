window.commentFormLogics = new function() {
    var commentsInfo = {};
    var initComponents = function() {
        var elements = document.querySelectorAll('.comment_form');
        for (var i = 0; i < elements.length; i++) {
            new CommentForm(elements[i]);
        }
        elements = document.querySelectorAll('.comment');
        for (i = 0; i < elements.length; i++) {
            if (elements[i].id.search('vote_id_') != -1) {
                var elementId = elements[i].id.split('_')[2];
                if (commentsInfo[elementId] != undefined) {
                    new CommentComponent(elements[i], commentsInfo[elementId]);
                }
            }
        }
    };
    var initLogics = function() {
        if (window.commentsList != undefined) {
            for (var i = 0; i < window.commentsList.length; i++) {
                commentsInfo[window.commentsList[i].id] = window.commentsList[i];
            }
        }
    };
    controller.addListener('initDom', initComponents);
    controller.addListener('initLogics', initLogics);
};