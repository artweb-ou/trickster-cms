window.CommentFormComponent = function(componentElement) {
    var init = function() {
        var userSelectElement = componentElement.querySelector('.comment_form_user_select');
        if (userSelectElement) {
            var types = userSelectElement.getAttribute('data-types');
            var apiMode = 'admin';
            new AjaxSelectComponent(userSelectElement, types, apiMode);
        }
    };
    init();
};