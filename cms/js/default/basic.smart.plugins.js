function empty(variable) {
    return (typeof variable === 'undefined' || variable === '' || variable === 0 || variable === '0' || variable === null || variable === false || (Array.isArray(variable) && variable.length === 0));
}

function isset(variable) {
    return !!variable;
}

function strtoupper(variable) {
    return variable.toUpperCase();
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
jSmart.prototype.registerPlugin(
    'block',
    'stripdomspaces',
    function(params, content, data, repeat) {
        return content.replace(/([}>])s+([{<])/u, '$1$2');
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
        data['theme'] = window.theme;
        data['selectedCurrencyItem'] = window.selectedCurrencyItem;
        return template.fetch(data);
    };
};

window.theme = new function() {
    var srcSetPresets = ['1.5', '2', '3'];
    var self = this;
    this.template = function(templateName) {
        return templateName;
    };
    this.generateImageUrl = function(imageId, fileName, type, multiplier) {
        var result = location.protocol + '//' + location.hostname + '/image/type:' + type + '/id:' + imageId;
        if (typeof multiplier !== 'undefined') {
            result += '/multiplier:' + multiplier;
        }
        result += '/' + fileName;
        return result;
    };
    this.generateImageSrcSet = function(imageId, fileName, type) {
        var urls = [];
        for (var i = 0; i < srcSetPresets.length; i++) {
            urls.push(self.generateImageUrl(imageId, fileName, type, srcSetPresets[i]) + ' ' + srcSetPresets[i] + 'x');
        }
        return urls.join(',');
    };

};