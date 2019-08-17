jSmart.prototype.registerPlugin(
    'function',
    'translations',
    function(params, data) {
        if ('name' in params) {
            return window.translationsLogics.get(params.name);
        }
        return false;
    }
);
jSmart.prototype.getTemplate = function(name) {
    if (typeof window.templates[name] !== 'undefined') {
        return window.templates[name];
    } else {
        return 'Missing JS Template ' + name;
    }
};

function empty(variable) {
    return (typeof variable === 'undefined' || variable === '' || variable === 0 || variable === '0' || variable === null || variable === false || (Array.isArray(variable) && variable.length === 0));
}