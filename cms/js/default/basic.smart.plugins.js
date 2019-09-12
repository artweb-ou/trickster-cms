function empty(variable) {
    return (typeof variable === 'undefined' || variable === '' || variable === 0 || variable === '0' || variable === null || variable === false || (Array.isArray(variable) && variable.length === 0));
}

function isset(variable) {
    return !!variable;
}

jSmart.prototype.registerPlugin(
    'function',
    'translations',
    function(params, data) {
        if ('name' in params) {
            return window.translationsLogics.get(params.name);
        }
        return false;
    },
);

jSmart.prototype.getTemplate = function(name) {
    return templatesManager.get(name);
};

window.smartyRenderer = new function() {
    var templates = {};
    this.fetch = function(name, data) {
        if (!templates[name]) {
            templates[name] = new jSmart(templatesManager.get(name));
        }

        var template = templates[name];
        data['theme'] = {
            template: function(templateName) {
                return templateName;
            },
        };
        return template.fetch(data);
    };
};