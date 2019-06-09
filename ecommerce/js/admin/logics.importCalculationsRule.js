window.importCalculationsRuleLogics = new function() {
    var rulesList = [];
    var importPluginsNames = [];
    var initLogics = function() {
        if (window.rulesData) {
            rulesList = window.rulesData.rules;
            importPluginsNames = window.rulesData.plugins;
        }
    };
    var initComponents = function() {
        var elements = _('.importcalculationsrule_form');
        for (var i = 0; i < elements.length; i++) {
            new ImportCalculationsRuleFormComponent(elements[i]);
        }
    };
    this.getRulesList = function() {
        return rulesList;
    };
    this.getImportPluginsNames = function() {
        return importPluginsNames;
    };
    controller.addListener('initLogics', initLogics);
    controller.addListener('initDom', initComponents);
};