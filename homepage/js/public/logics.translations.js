window.translationsLogics = new function() {
    var translationsList = {};
    var init = function() {
        if (typeof window.translations !== 'undefined') {
            translationsList = window.translations;
        }
    };
    this.get = function(name) {
        if (typeof translationsList[name] !== 'undefined') {
            return translationsList[name];
        } else {
            return '#' + name + '#';
        }
    };
    controller.addListener('initLogics', init);
};