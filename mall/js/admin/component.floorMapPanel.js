window.FloorMapPanelComponent = function(componentElement) {
    var self = this;
    var deleteButton;
    var editButton;
    var cancelButton;
    var tabsBlock;
    var undoControls;
    var iconWidthElement, iconHeightElement, precisionElement, iconAngleElement;

    var init = function() {
        tabsBlock = new TabsBlockComponent(_('.floor_mapeditor_panel_tabs')[0]);
        tabsBlock.setChangeHandler(tabChanged);
        editButton = new FloorMapEditButtonComponent(_('.edit_button', componentElement)[0]);
        cancelButton = new FloorMapCancelButtonComponent(_('.cancel_button', componentElement)[0]);
        deleteButton = new FloorMapDeleteButtonComponent(_('.delete_button', componentElement)[0]);
        undoControls = new FloorMapUndoControlsComponent(_('.floor_mapeditor_panel_undocontrols', componentElement)[0]);
        iconWidthElement = _('.floor_mapeditor_panel_icon_width')[0];
        iconHeightElement = _('.floor_mapeditor_panel_icon_height')[0];
        iconAngleElement = _('.floor_mapeditor_panel_icon_angle')[0];
        precisionElement = _('.floor_mapeditor_panel_precision')[0];
        floorMapLogics.setSelectorType(tabsBlock.getCurrentTabNumber());
        controller.addListener('editingModeChanged', editingModeChanged);
        controller.addListener('roomSelected', roomSelected);
        controller.addListener('iconSelected', iconSelected);
        controller.addListener('objectSelected', objectSelected);
        controller.addListener('roomUpdated', roomUpdated);
        controller.addListener('editingCompleted', editingCompleted);
    };
    var tabChanged = function() {
        floorMapLogics.setSelectorType(tabsBlock.getCurrentTabNumber());
        floorMapLogics.disableEditingMode();
        floorMapLogics.selectObject(false);

        if (tabsBlock.getCurrentTabNumber() == 0) {
            floorMapLogics.selectRoom(window.roomSelectorComponent.componentElement.value);
        } else {
            floorMapLogics.selectIcon(window.iconSelectorComponent.componentElement.value);
        }
    };
    var editingModeChanged = function() {
        refreshButtons();
    };
    var roomSelected = function() {
        floorMapLogics.cancelEditing();
        refreshButtons();
    };
    var iconSelected = function() {
        floorMapLogics.cancelEditing();
        refreshButtons();
    };
    var editingCompleted = function() {
        refreshButtons();
    };
    var objectSelected = function(object) {
        if (object) {
            deleteButton.enable();
        } else {
            deleteButton.disable();
        }
        refreshButtons();
    };
    var roomUpdated = function() {
        refreshButtons();
    };
    var refreshButtons = function() {
        var editingMode = window.floorMapLogics.getEditingMode();
        if (editingMode) {
            deleteButton.disable();
            cancelButton.enable();
            editButton.setTitle('Save');

            var room = window.floorMapLogics.getSelectedRoom();
            if (room) {
                if (room.nodes && room.nodes.length == 0) {
                    editButton.enable();
                } else {
                    editButton.disable();
                }
            }
        } else {
            cancelButton.disable();
            editButton.setTitle('Create');
            if (window.floorMapLogics.getSelectedIcon() || (window.floorMapLogics.getSelectedRoom()) && !floorMapLogics.getSelectedObject()) {
                editButton.enable();
            } else {
                editButton.disable();
            }

            if (floorMapLogics.getSelectedObject()) {
                deleteButton.enable();
            } else {
                deleteButton.disable();
            }
        }
    };
    this.getIconWidth = function() {
        return iconWidthElement.value;
    };
    this.getIconHeight = function() {
        return iconHeightElement.value;
    };
    this.getIconAngle = function() {
        return parseInt(iconAngleElement.value) || 0;
    };
    this.getPrecision = function() {
        return parseFloat(precisionElement.value) || 0;
    };
    init();
};

window.FloorMapPanelButtonMixin = function(componentElement) {
    this.CLASS_DISABLED = 'button_disabled';
    this.enabled = false;
    var self = this;

    this.enable = function() {
        this.removeClass(self.CLASS_DISABLED);
        componentElement.disabled = false;
        self.enabled = true;
    };
    this.disable = function() {
        this.addClass(self.CLASS_DISABLED);
        componentElement.disabled = true;
        self.enabled = false;
    };
    this.setTitle = function(newTitle) {
        componentElement.value = newTitle;
    };
    this.addClass = function(className) {
        domHelper.addClass(componentElement, className);
    };
    this.removeClass = function(className) {
        domHelper.removeClass(componentElement, className);
    };
};

window.FloorMapCancelButtonComponent = function(componentElement) {
    FloorMapPanelButtonMixin.call(this, componentElement);

    var self = this;

    var init = function() {
        self.disable();
        window.eventsManager.addHandler(componentElement, 'click', self.clickHandler);
    };
    this.clickHandler = function() {
        if (self.enabled) {
            window.floorMapLogics.cancelEditing();
        }
    };
    init();
};

window.FloorMapDeleteButtonComponent = function(componentElement) {
    FloorMapPanelButtonMixin.call(this, componentElement);

    var self = this;

    var init = function() {
        self.disable();
        window.eventsManager.addHandler(componentElement, 'click', self.clickHandler);
    };
    this.clickHandler = function() {
        if (!componentElement.disabled) {
            var object = window.floorMapLogics.getSelectedObject();
            if (object) {
                if (object instanceof sectionRegionComponent) {
                    var confirmText = 'Are you sure you want to delete "' + object.title + '"?';
                    if (confirm(confirmText)) {
                        window.floorMapLogics.deleteRoom(object.id);
                        floorMapLogics.selectObject(false);
                    }
                } else if (object instanceof FloorMapIconImageComponent) {
                    var confirmText = 'Are you sure you want to delete this icon?';
                    if (confirm(confirmText)) {
                        object.remove();
                        var map = window.floorMapLogics.getMap();
                        var iconImages = map.getIconImages();
                        if (iconImages[object.id]) {
                            for (var i = iconImages[object.id].length; i--;) {
                                if (iconImages[object.id][i] == object) {
                                    iconImages[object.id].splice(i, 1);
                                    window.floorMapLogics.persistIcons(iconImages[object.id]);
                                    floorMapLogics.selectObject(false);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    };
    init();
};

window.FloorMapEditButtonComponent = function(componentElement) {
    FloorMapPanelButtonMixin.call(this, componentElement);

    var self = this;
    this.editingMode = false;

    var init = function() {
        self.disable();
        window.eventsManager.addHandler(componentElement, 'click', self.clickHandler);
    };
    this.clickHandler = function() {
        if (self.enabled) {
            var editingMode = window.floorMapLogics.getEditingMode();
            if (editingMode) {
                window.floorMapLogics.completeEditing();
            } else {
                window.floorMapLogics.enableEditingMode();
            }
        }
    };
    init();
};

window.iconSelectorComponent = new function() {
    var self = this;
    var icons = [];
    this.componentElement = false;

    this.initHandler = function() {
        if (self.createDomStructure()) {
            icons = window.floorMapLogics.getIconsList();
            self.updateData();
            window.eventsManager.addHandler(self.componentElement, 'change', self.changeHandler);
            controller.addListener('iconSelected', self.iconSelectedHandler);
        }
    };
    this.createDomStructure = function() {
        var result = false;
        if (self.componentElement = _('select.icon_selector')[0]) {
            result = true;
        }
        return result;
    };
    this.updateData = function() {
        var emptyOption = document.createElement('option');
        emptyOption.value = '0';
        emptyOption.text = 'Select icon';
        self.componentElement.appendChild(emptyOption);

        for (var i = 0; i < icons.length; i++) {
            var optionElement = self.createOption(icons[i]);
            self.componentElement.appendChild(optionElement);
        }
        dropDownManager.updateDropDown(self.componentElement);
    };
    this.createOption = function(data) {
        var element = document.createElement('option');
        element.value = data.id;
        element.text = data.title;
        return element;
    };
    this.changeHandler = function() {
        floorMapLogics.selectObject(false);
        window.floorMapLogics.selectIcon(self.componentElement.value);
    };
    this.iconSelectedHandler = function(iconId) {
        if (self.componentElement.value != iconId) {
            self.componentElement.value = iconId;
        }
    };
    controller.addListener('startApplication', this.initHandler);
};

window.roomSelectorComponent = new function() {
    var self = this;

    this.roomsList = false;
    this.componentElement = false;

    this.initHandler = function() {
        if (self.createDomStructure()) {
            self.roomsList = window.floorMapLogics.getRoomsList();
            self.updateData();

            window.eventsManager.addHandler(self.componentElement, 'change', self.changeHandler);
            controller.addListener('roomSelected', self.roomSelectedHandler);
        }
    };
    this.createDomStructure = function() {
        var result = false;
        if (self.componentElement = _('select.floor_mapeditor_panel_precision_room_selector')[0]) {
            result = true;
        }
        return result;
    };
    this.updateData = function() {
        var emptyOption = document.createElement('option');
        emptyOption.value = '0';
        emptyOption.text = 'Select area';
        self.componentElement.appendChild(emptyOption);

        for (var i = 0; i < self.roomsList.length; i++) {
            var optionElement = self.createOption(self.roomsList[i]);
            self.componentElement.appendChild(optionElement);
        }
        dropDownManager.updateDropDown(self.componentElement);

    };
    this.createOption = function(data) {
        var element = document.createElement('option');
        element.value = data.id;
        element.text = data.title;
        return element;
    };
    this.changeHandler = function() {
        floorMapLogics.selectObject(false);
        window.floorMapLogics.selectRoom(self.componentElement.value);
    };
    this.roomSelectedHandler = function(roomId) {
        if (self.componentElement.value != roomId) {
            self.componentElement.value = roomId;
        }
    };
    controller.addListener('startApplication', this.initHandler);
};

window.FloorMapUndoControlsComponent = function(componentElement) {
    var self = this;
    this.componentElement = false;
    this.undoButton = false;
    this.redoButton = false;

    var init = function() {
        createDomStructure();
        self.refreshStatus();
        controller.addListener('editingModeChanged', self.refreshStatus);
        controller.addListener('undoStateChanged', self.refreshStatus);
        window.eventsManager.addHandler(document, 'keydown', self.keyHandler);
    };
    this.keyHandler = function(event) {
        if (event.keyCode == '90' && event.ctrlKey) //ctrl+z
        {
            window.undoManagerLogics.performUndo();
        } else if (event.keyCode == '89' && event.ctrlKey) //ctrl+y
        {
            window.undoManagerLogics.performRedo();
        }
    };
    var createDomStructure = function() {
        if (self.undoButton = _('.undo_button', componentElement)[0]) {
            window.eventsManager.addHandler(self.undoButton, 'click', self.undoClickHandler);
        }
        if (self.redoButton = _('.redo_button', componentElement)[0]) {
            window.eventsManager.addHandler(self.redoButton, 'click', self.redoClickHandler);
        }
    };
    this.undoClickHandler = function() {
        window.undoManagerLogics.performUndo();
    };
    this.redoClickHandler = function() {
        window.undoManagerLogics.performRedo();
    };
    this.refreshStatus = function() {
        var undoActive = false;
        var redoActive = false;

        var undoCount = window.undoManagerLogics.getUndoCount();
        var redoCount = window.undoManagerLogics.getRedoCount();
        var editingMode = window.floorMapLogics.getEditingMode();

        if (editingMode) {
            if (undoCount > 0) {
                undoActive = true;
            }
            if (redoCount > 0) {
                redoActive = true;
            }
        }

        if (undoActive) {
            self.undoButton.disabled = false;
            self.undoButton.className = 'button undo_button';
        } else {
            self.undoButton.disabled = true;
            self.undoButton.className = 'button undo_button button_disabled';
        }
        if (redoActive) {
            self.redoButton.disabled = false;
            self.redoButton.className = 'button redo_button';
        } else {
            self.redoButton.disabled = true;
            self.redoButton.className = 'button redo_button button_disabled';
        }
    };
    init();
};