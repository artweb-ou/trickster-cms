jSmart.prototype.registerPlugin('function', 'translations', function (params, data) {
    return translationsLogics.get(params.name);
});
jSmart.prototype.getTemplate = function (name) {
    return templatesManager.get(name);
};

window.smartyRenderer = new function () {
    var templates = {};
    var currentTemplate = null;

    this.setTemplate = function (name) {
        if (!templates[name])
            templates[name] = new jSmart(templatesManager.get(name));
        currentTemplate = templates[name];
    };
    this.fetch = function (data) {
        if (!currentTemplate) {
            return '';
        }
        data['theme'] = {
            template: function (templateName) {
                return templateName;
            }
        };
        data['isset'] = function (argument) {
            return !!argument
        };
        return currentTemplate.fetch(data);
    };
};