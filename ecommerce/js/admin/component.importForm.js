window.ImportFormComponent = function(componentElement) {
    var self = this;
    var index = 0;
    var recordsElement;

    var init = function() {
        recordsElement = componentElement.querySelector('.importform_form_records');
        new ImportFormAdderComponent(componentElement.querySelector('.importform_form_adder'), self);

        var recordsInfo = importFormLogics.getRecords();
        if (recordsInfo.length > 0) {
            for (var i = 0; i < recordsInfo.length; ++i) {
                self.addRecord(recordsInfo[i].importOrigin, recordsInfo[i].importId);
            }
        }
    };
    this.addRecord = function(origin, id) {
        var record = new ImportFormRecordComponent(index, origin, id);
        recordsElement.appendChild(record.getComponentElement());
        ++index;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};

window.ImportFormAdderComponent = function(componentElement, form) {
    var originElement, idElement, addButtonElement;

    var init = function() {
        originElement = componentElement.querySelector('select.importform_form_adder_origin');
        idElement = componentElement.querySelector('.importform_form_adder_id');
        addButtonElement = componentElement.querySelector('.importform_form_adder_add');
        eventsManager.addHandler(addButtonElement, 'click', addClick);
    };
    var addClick = function(event) {
        eventsManager.preventDefaultAction(event);
        form.addRecord(originElement.value, idElement.value);
        idElement.value = '';
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};

window.ImportFormRecordComponent = function(index, origin, id) {
    var self = this;
    var componentElement;
    var formNameBase = '';

    var init = function() {
        formNameBase = 'formData[' + window.currentElementId + '][importInfo][' + index + ']';
        componentElement = self.makeElement('div', 'form_items');
        self.makeElement('div', 'importform_form_record_origin form_field', componentElement).appendChild(createInput('importOrigin', origin));
        self.makeElement('div', 'importform_form_record_import_id form_field', componentElement).appendChild(createInput('importId', id));
        var removeButtonElement = self.makeElement('a', 'importform_form_record_remove_button fields_row_remove icon icon_delete');
        self.makeElement('div', 'importform_form_record_remove form_field', componentElement).appendChild(removeButtonElement);
        removeButtonElement.href = '#';
        eventsManager.addHandler(removeButtonElement, 'click', removeClick);
    };
    var removeClick = function(event) {
        eventsManager.preventDefaultAction(event);
        if (componentElement.parentNode) {
            componentElement.parentNode.removeChild(componentElement);
        }
    };
    var createInput = function(name, value) {
        var inputElement = self.makeElement('input', 'input_component');
        inputElement.name = formNameBase + '[' + name + ']';
        inputElement.value = value;
        return inputElement;
    };
    this.getComponentElement = function() {
        return componentElement;
    };
    init();
};
DomElementMakerMixin.call(ImportFormRecordComponent.prototype);