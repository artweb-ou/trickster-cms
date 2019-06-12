window.debugLogics = new function() {
    this.addRecord = function(text) {
        var record = {};
        record['text'] = text;
        records.push(record);
        controller.fireEvent('debugInfoUpdate');
    };
    this.getRecords = function() {
        return records;
    };
    this.getLastRecord = function() {
        return records[records.length - 1];
    };
    var self = this;
    var records = [];
};
window.debug = function() {
    var text = '';

    for (var i = 0; i < arguments.length; i++) {
        var value = arguments[i];
        if (typeof value == 'object') {
            for (var j in value) {
                text += j + ': ' + value[j] + '\n';
            }
        } else if (typeof value == 'function') {
            text += 'function: ' + value + ' ';
        } else {
            text += value + ' ';
        }
    }
    window.debugLogics.addRecord(text);
};