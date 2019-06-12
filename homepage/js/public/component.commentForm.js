window.CommentForm = function(element) {
    this.submit = function() {
        self.element.submit();
    };
    this.init = function() {
        var sendButton = _('.form_button', this.element)[0];
        eventsManager.addHandler(sendButton, 'click', this.submit);
    };
    var self = this;
    this.element = element;
    this.init();
};