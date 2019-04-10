window.floorMapLogics = new function() {
	var self = this;
	var roomsIndex = {};
	var roomsList = [];
	var iconsIndex = {};
	var iconsList = [];
	var selectedRoomId = false;
	var selectedIconId = false;
	var selectedObject;
	var editingMode = false;
	var mapComponent;
	var panelComponent;
	var selectorType = 0;

	this.initHandler = function() {
		if (window.editorInfo) {
			var baseNodes = window.editorInfo.baseNodes;
			roomsList = window.editorInfo.rooms;
			roomsList.unshift({
				'id':'base',
				'title':'Base',
				'nodes':baseNodes
			})
			for (var i = 0; i < roomsList.length; i++) {
				roomsIndex[roomsList[i].id] = roomsList[i];
			}
			iconsList = window.editorInfo.icons;
			for (var i = 0; i < iconsList.length; i++) {
				iconsIndex[iconsList[i].id] = iconsList[i];
			}
		}
		controller.addListener('initDom', initComponents);
	};
	var initComponents = function() {
		var element = _('.floor_mapeditor_map')[0];
		if (element) {
			mapComponent = new FloorMapComponent(element);
			element = _('.floor_mapeditor_panel')[0];
			panelComponent = new FloorMapPanelComponent(element);
		}
	};
	this.getMap = function() {
		return mapComponent;
	};
	this.setSelectorType = function(newSelectorType) {
		selectorType = newSelectorType;
	};
	this.getSelectorType = function() {
		return selectorType;
	};
	this.getIconsList = function() {
		return iconsList;
	};
	this.getRoomsList = function() {
		return roomsList;
	};
	this.selectObject = function(object) {
		selectedObject = object;
		controller.fireEvent('objectSelected', object);
	};
	this.getSelectedObject = function() {
		return selectedObject;
	};
	this.getRoomNodesList = function(id) {
		var result = false;
		if (roomsIndex[id]) {
			result = roomsIndex[id].nodes;
		}
		return result;
	};
	this.selectRoom = function(roomId) {
		selectedIconId = 0;
		selectedRoomId = roomId;
		controller.fireEvent('roomSelected', roomId);
	};
	this.selectIcon = function(iconId) {
		selectedRoomId = 0;
		selectedIconId = iconId;
		controller.fireEvent('iconSelected', iconId);
	};
	this.getSelectedRoom = function() {
		var result = false;
		if (roomsIndex[selectedRoomId]) {
			result = roomsIndex[selectedRoomId];
		}
		return result;
	};
	this.getSelectedIcon = function() {
		var result = false;
		if (iconsIndex[selectedIconId]) {
			result = iconsIndex[selectedIconId];
		}
		return result;
	};
	this.deleteRoom = function(id) {
		var parameters = {};
		parameters['roomId'] = id;
		sendData('deleteRoom', parameters);
	};
	this.completeEditing = function() {
		controller.fireEvent('editingCompleted');
	};
	this.cancelEditing = function() {
		self.disableEditingMode();
		controller.fireEvent('editingCancelled');
	};
	this.enableEditingMode = function() {
		editingMode = true;
		controller.fireEvent('editingModeChanged');
	};
	this.disableEditingMode = function() {
		editingMode = false;
		controller.fireEvent('editingModeChanged');
	};
	this.getEditingMode = function() {
		return editingMode;
	};
	this.persistRoom = function(nodes) {
		var requestParameters = {};
		requestParameters['roomId'] = selectedRoomId;
		for (var i = 0; i < nodes.length; i++) {
			requestParameters['nodesInput[' + nodes[i].number + '][x]'] = nodes[i].x;
			requestParameters['nodesInput[' + nodes[i].number + '][y]'] = nodes[i].y;
			requestParameters['nodesInput[' + nodes[i].number + '][number]'] = nodes[i].number;
		}
		sendData('addRoom', requestParameters);
	};
	this.persistIcons = function(icons) {
		var requestParameters = {};
		requestParameters['iconId'] = selectedIconId;
		for (var i = 0; i < icons.length; i++) {
			requestParameters['nodesInput[' + i + '][x]'] = icons[i].x;
			requestParameters['nodesInput[' + i + '][y]'] = icons[i].y;
			requestParameters['nodesInput[' + i + '][rotation]'] = icons[i].rotation;
			requestParameters['nodesInput[' + i + '][width]'] = icons[i].width;
			requestParameters['nodesInput[' + i + '][height]'] = icons[i].height;
			requestParameters['nodesInput[' + i + '][number]'] = i;
		}
		sendData('saveIcon', requestParameters);
	};
	var sendData = function(actionName, requestParameters) {
		requestParameters['editAction'] = actionName;
		var requestURL = window.ajaxURL + 'id:' + window.elementId + '/action:editMap';
		var request = new JsonRequest(requestURL, receiveData, 'editMap', requestParameters);
		request.send();
	};

	var receiveData = function(responseStatus, requestName, parsedData) {
		if (responseStatus == 'success' && requestName == 'editMap') {
			switch(parsedData.editAction) {
				case 'addRoom':
					var roomId = parsedData.roomId;
					if (roomsIndex[roomId]) {
						roomsIndex[roomId].nodes = parsedData.nodes;
					}
					break;
				case 'deleteRoom':
					var roomId = parsedData.roomId;
					if (roomsIndex[roomId]) {
						roomsIndex[roomId].nodes = [];
					}
					break;
			}
			controller.fireEvent('roomUpdated', roomId);
			window.floorMapLogics.disableEditingMode();
		}
	};
	this.getPanel = function() {
		return panelComponent;
	};
	controller.addListener('initLogics', this.initHandler);
};