window.roomsMapLogics = new function() {
	var self = this;

	var currentQuery;

	var floorsList = [];
	var floorsIndex = {};

	var roomsList = [];
	var roomsIndex = {};
	var shopsList = [];
	var shopsIndex = {};
	var roomsExportIndex;
	var roomShopIndex = {};
	var iconsList = [];
	var iconsIndex = {};

	var categoriesList = [];
	var categoriesIndex = {};

	var currentFloorNumber;
	var currentCategoryId;
	var currentRoomId;
	var calculatedShopsList = [];
	var roomsMapComponents = [];
	var mapUrl = '';
	this.DEFAULT_ROOM_COLOR = '#f0f0f7';

	var initLogics = function() {
		if (typeof window.roomsMapInfo == 'object') {
			importInfo(window.roomsMapInfo);
			controller.addListener("anchorParametersUpdate", anchorParametersUpdateHandler);
			currentFloorNumber = 0;
		}
	};
	var anchorParametersUpdateHandler = function(parameters) {
		var roomsMapStateChanged = false;
		if (parameters.category) {
			if (categoryChangeHandler(parameters.category)) {
				roomsMapStateChanged = true;
			}
		} else {
			if (categoryChangeHandler(null)) {
				roomsMapStateChanged = true;
			}
		}
		if (parameters.floor) {
			if (floorChangeHandler(parameters.floor)) {
				roomsMapStateChanged = true;
			}
		}

		if (parameters.room) {
			if (roomChangeHandler(parameters.room)) {
				roomsMapStateChanged = true;
			}
		} else {
			if (roomChangeHandler(null)) {
				roomsMapStateChanged = true;
			}
		}
		if (parameters.search) {
			if (searchHandler(parameters.search)) {
				roomsMapStateChanged = true;
			}
		} else {
			if (searchHandler(null)) {
				roomsMapStateChanged = true;
			}
		}
		if (roomsMapStateChanged) {
			for (var i = 0; i < floorsList.length; i++) {
				floorsList[i].recalculateUrl(currentCategoryId, currentQuery, currentRoomId);
			}
			controller.fireEvent('roomsMapStateChanged');
		}

		recalculateRoomsList();

		if (parameters.reset) {
			if (parameters.reset == '1') {
				self.resetComponentsLayout();
			}
		}
	};
	var startHandler = function() {
		controller.fireEvent('roomsMapStateChanged');
	};
	var categoryChangeHandler = function(categoryId) {
		if (currentCategoryId != categoryId) {
			currentCategoryId = categoryId;
			controller.fireEvent('roomsCategoryChanged', currentCategoryId);
			return true;
		}
		return false;
	};
	var searchHandler = function(query) {
		if (currentQuery != query) {
			currentQuery = query;
			controller.fireEvent('roomsSearchQueryChanged', currentQuery);
			return true;
		}
		return false;
	};
	var floorChangeHandler = function(floorId) {
		if (currentFloorNumber != floorId) {
			currentFloorNumber = floorId;
			controller.fireEvent('roomsFloorChanged', currentFloorNumber);
			return true;
		}
		return false;
	};
	var roomChangeHandler = function(roomId) {
		if (currentRoomId != roomId) {
			currentRoomId = roomId;
			controller.fireEvent('roomChanged', currentRoomId);
			return true;
		}
		return false;
	};
	var recalculateRoomsList = function() {
		calculatedShopsList = [];
		if (currentCategoryId) {
			for (var i = 0; i < shopsList.length; i++) {
				if (shopsList[i].categoryId == currentCategoryId) {
					calculatedShopsList.push(shopsList[i]);
				}
			}
		} else if (currentQuery) {
			for (var i = 0; i < shopsList.length; i++) {
				var expression = new RegExp(currentQuery, 'i');
				if (shopsList[i].title.search(expression) != -1) {
					calculatedShopsList.push(shopsList[i]);
				}
			}
		}
		controller.fireEvent('roomsListRecalculated', calculatedShopsList);
	};
	var importInfo = function(info) {
		if (typeof info.mapUrl) {
			mapUrl = info.mapUrl;
		}
		if (typeof info.categories == 'object') {
			categoriesList = [];
			categoriesIndex = {};
			for (var i = 0; i < info.categories.length; i++) {
				var category = new RoomsMapCategory(info.categories[i]);
				categoriesList.push(category);
				categoriesIndex[category.id] = category;
			}
		}
		if (typeof info.floors == 'object') {
			floorsList = [];
			floorsIndex = {};
			for (var i = 0; i < info.floors.length; i++) {
				var floor = new RoomsMapFloor(info.floors[i]);
				floorsList.push(floor);
				floorsIndex[floor.id] = floor;
			}
		}
		if (typeof info.icons == 'object') {
			iconsList = [];
			iconsIndex = {};
			for (var i = 0; i < info.icons.length; i++) {
				var icon = new RoomsMapIcon(info.icons[i]);
				iconsList.push(icon);
				iconsIndex[icon.id] = icon;
			}
		}
		if (typeof info.rooms == 'object') {
			roomsList = [];
			roomsIndex = {};
			roomsExportIndex = {};
			for (var i = 0; i < info.rooms.length; i++) {
				var room = new RoomsMapRoom(info.rooms[i]);
				roomsList.push(room);
				roomsIndex[room.id] = room;
				roomsExportIndex[room.floorNumber + '_' + room.number] = room;
			}
		}
		if (typeof info.shops == 'object') {
			shopsList = [];
			shopsIndex = {};
			roomShopIndex = {};
			for (var i = 0; i < info.shops.length; i++) {
				var shop = new RoomsMapShop(info.shops[i]);
				shopsList.push(shop);
				shopsIndex[shop.id] = shop;
				for (var j = shop.roomsIds.length; j--;) {
					var roomId = shop.roomsIds[j];
					roomShopIndex[roomId] = shop;
				}
			}
		}
	};

	var initComponents = function() {
		var elements = _('.roomsmap_block');
		for (var i = 0; i < elements.length; i++) {
			roomsMapComponents.push(new RoomsMapComponent(elements[i]));
		}
		var elements = _('.floor_plan_controls');
		for (var i = elements.length; i--;) {
			new FloorMapControlsComponent(elements[i]);
		}
	};
	this.getRoomByExportId = function(id) {
		var result = null;
		if (typeof roomsExportIndex[id] != 'undefined') {
			result = roomsExportIndex[id];
		}
		return result;
	};
	this.getCategory = function(id) {
		var category = false;
		if (typeof categoriesIndex[id] != undefined) {
			category = categoriesIndex[id];
		}
		return category;
	};
	this.getRoom = function(id) {
		var room = false;
		if (typeof roomsIndex[id] != undefined) {
			room = roomsIndex[id];
		}
		return room;
	};
	this.getIcon = function(id) {
		var icon = false;
		if (typeof iconsIndex[id] != undefined) {
			icon = iconsIndex[id];
		}
		return icon;
	};
	this.getShop = function(id) {
		var shop = false;
		if (typeof shopsIndex[id] != undefined) {
			shop = shopsIndex[id];
		}
		return shop;
	};
	this.getCurrentRoom = function() {
		var result = null;
		if (typeof roomsIndex[currentRoomId] != 'undefined') {
			result = roomsIndex[currentRoomId];
		}
		return result;
	};
	this.getShopByRoomId = function(id) {
		var room = false;
		if (typeof roomShopIndex[id] != 'undefined') {
			room = roomShopIndex[id];
		}
		return room;
	};
	this.getCurrentFloorNumber = function() {
		return currentFloorNumber;
	};
	this.getCurrentFloor = function() {
		return self.getFloorByNumber(currentFloorNumber);
	};
	this.getFloor = function(id) {
		if (typeof floorsIndex[id] != 'undefined') {
			return floorsIndex[id];
		}
		return false;
	};
	this.getFloorByNumber = function(number) {
		for (var i = 0; i < floorsList.length; i++) {
			if (floorsList[i].number == number) {
				return floorsList[i];
			}
		}
		return false;
	};
	this.getCurrentCategoryId = function() {
		return currentCategoryId;
	};
	this.getCurrentQuery = function() {
		return currentQuery;
	};
	this.getCategoriesList = function() {
		return categoriesList;
	};
	this.getShopsList = function() {
		return calculatedShopsList;
	};
	this.getFloorsList = function() {
		return floorsList;
	};
	this.getRoomsList = function() {
		return roomsList;
	};
	this.resetComponentsLayout = function() {
		for (var i = 0; i < roomsMapComponents.length; i++) {
			roomsMapComponents[i].resetLayout();
		}
	};
	this.getMapUrl = function() {
		return mapUrl;
	};
	controller.addListener('initLogics', initLogics);
	controller.addListener('initDom', initComponents);
	controller.addListener('startApplication', startHandler);
};
window.RoomsMapCategory = function(info) {
	var self = this;
	this.id = null;
	this.url = null;
	this.title = null;

	this.color = null;
	this.colorTop = null;
	this.colorStroke = null;

	this.colorTopOver = null;
	this.colorStrokeOver = null;

	this.colorTopActive = null;
	this.colorStrokeActive = null;

	var init = function() {
		importInfo(info);
	};
	var importInfo = function(importedInfo) {
		self.id = parseInt(importedInfo.id, 10);
		self.title = importedInfo.title;
		self.color = importedInfo.color;
		self.url = roomsMapLogics.getMapUrl() + '#category=' + self.id;
		self.colorTop = importedInfo.color;
		if (self.colorTop) {
			self.colorTop = '#' + self.colorTop;
		} else {
			self.colorTop = roomsMapLogics.DEFAULT_ROOM_COLOR;
		}
		self.colorDark = calculateColor(self.colorTop, 0.18, false);
		self.colorMedium = calculateColor(self.colorTop, 0.14, false);
		self.colorLight = calculateColor(self.colorTop, 0.2, true);
		self.colorStroke = calculateColor(self.colorTop, 0.1, false);

		self.colorTopOver = calculateColor(self.colorTop, 0.15, true);
		self.colorDarkOver = calculateColor(self.colorTopOver, 0.18, false);
		self.colorMediumOver = calculateColor(self.colorTopOver, 0.14, false);
		self.colorLightOver = calculateColor(self.colorTopOver, 0.2, true);
		self.colorStrokeOver = calculateColor(self.colorTopOver, 0.1, false);

		self.colorTopActive = calculateColor(self.colorTop, 0.4, true);
		self.colorDarkActive = calculateColor(self.colorTopActive, 0.18, false);
		self.colorMediumActive = calculateColor(self.colorTopActive, 0.14, false);
		self.colorLightActive = calculateColor(self.colorTopActive, 0.2, true);
		self.colorStrokeActive = calculateColor(self.colorTopActive, 0.1, false);
	};
	var pad = function(num, totalChars) {
		var pad = '0';
		num = num + '';
		while (num.length < totalChars) {
			num = pad + num;
		}
		return num;
	};
	var calculateColor = function(color, percentage, lighter) {
		var r = parseInt(color.substring(1, 3), 16);
		var g = parseInt(color.substring(3, 5), 16);
		var b = parseInt(color.substring(5, 7), 16);

		if (lighter) {
			r = Math.min(r + r * percentage, 255);
			g = Math.min(g + g * percentage, 255);
			b = Math.min(b + b * percentage, 255);
		} else {
			r = Math.max(r - r * percentage, 0);
			g = Math.max(g - g * percentage, 0);
			b = Math.max(b - b * percentage, 0);
		}

		return '#' + pad(Math.round(r).toString(16), 2) + pad(Math.round(g).toString(16), 2) + pad(Math.round(b).toString(16), 2);
	};
	this.getUrl = function(viewName) {
		var url = self.url;
		if (viewName) {
			url += '&view=' + viewName;
		}
		return url;
	};
	init();
};
window.RoomsMapFloor = function(info) {
	var self = this;
	this.id = null;
	this.url = null;
	this.title = null;

	var init = function() {
		importInfo(info);
	};
	var importInfo = function(importedInfo) {
		self.id = importedInfo.id;
		self.number = importedInfo.number;
		self.title = importedInfo.title;
		self.mapInfo = importedInfo.mapInfo;
		self.url = roomsMapLogics.getMapUrl() + '#floor=' + self.number;
	};
	this.getTitle = function() {
		return self.title;
	};
	this.recalculateUrl = function(currentCategoryId, currentQuery, currentRoomId) {
		self.url = roomsMapLogics.getMapUrl() + '#floor=' + self.number;
		if (currentCategoryId) {
			self.url += '&category=' + currentCategoryId;
		}
		if (currentQuery) {
			self.url += '&search=' + currentQuery;
		}
		if (currentRoomId) {
			self.url += '&room=' + currentRoomId;
		}
	};
	init();
};
window.RoomsMapRoom = function(info) {
	var self = this;

	this.title = null;
	this.number = null;
	this.id = null;
	this.floorId = null;
	this.floorNumber = null;
	this.shopId = [];

	var init = function() {
		importInfo(info);
	};
	var importInfo = function(importedInfo) {
		self.floorId = parseInt(importedInfo.floorId, 10);
		self.floorNumber = importedInfo.floorNumber;
		self.id = importedInfo.id;
		self.number = importedInfo.number;
		self.title = importedInfo.title;
		self.shopId = importedInfo.shopId;
	};
	this.getRoomUrl = function(forceFloor, forceView) {
		var url = roomsMapLogics.getMapUrl() + '#';
		if (forceView) {
			url += 'view=' + forceView;
		} else {
			url += 'view=plan';
		}

		url += '&room=' + self.id;

		if (forceFloor) {
			url += '&floor=' + forceFloor;
		} else {
			url += '&floor=' + self.floorNumber;
		}

		var currentCategoryId = roomsMapLogics.getCurrentCategoryId();
		if (currentCategoryId) {
			url += '&category=' + currentCategoryId;
		}
		var currentQuery = roomsMapLogics.getCurrentQuery();
		if (currentQuery) {
			url += '&search=' + currentQuery;
		}

		return url;
	};
	this.getShop = function() {
		var result = null;
		if (self.shopId > 0) {
			result = window.roomsMapLogics.getShop(self.shopId);
		}
		return result;
	};
	this.getFloor = function() {
		return roomsMapLogics.getFloor(self.floorId);
	};
	init();
};

window.RoomsMapShop = function(info) {
	var self = this;

	this.floorId = null;
	this.number = null;
	this.vectorId = null;
	this.categoryId = null;
	this.title = null;
	this.introduction = null;
	this.openedTime = null;
	this.openeningHoursInfo = null;
	this.contactInfo = null;
	this.image = null;
	this.logo = null;
	this.id = null;
	this.URL = null;
	this.campaigns = [];

	var init = function() {
		importInfo(info);
	};
	var importInfo = function(importedInfo) {
		self.categoryId = parseInt(importedInfo.categoryId, 10);
		self.vectorId = '';
		self.title = importedInfo.title;
		self.id = importedInfo.id;
		self.introduction = importedInfo.introduction;
		self.openedTime = importedInfo.openedTime;
		self.openingHoursInfo = importedInfo.openingHoursInfo;
		self.contactInfo = importedInfo.contactInfo;
		self.image = importedInfo.image;
		self.logo = importedInfo.logo;
		self.URL = importedInfo.URL;
		self.roomsIds = importedInfo.roomsIds;

		if (importedInfo.campaigns) {
			self.campaigns = importedInfo.campaigns;
		}

	};
	this.getShopUrl = function(forceFloor, forceView, reset) {
		var url = roomsMapLogics.getMapUrl() + '#';
		if (forceView) {
			url += 'view=' + forceView;
		} else {
			url += 'view=plan';
		}

		url += '&room=' + self.getRoom().id;
		if (forceFloor) {
			url += '&floor=' + forceFloor;
		} else {
			url += '&floor=' + self.getFloorNumber();
		}

		var currentCategoryId = roomsMapLogics.getCurrentCategoryId();
		if (currentCategoryId) {
			url += '&category=' + currentCategoryId;
		}
		var currentQuery = roomsMapLogics.getCurrentQuery();
		if (currentQuery) {
			url += '&search=' + currentQuery;
		}
		if (reset) {
			url += '&reset=1';
		}
		return url;
	};
	this.getRoom = function() {
		var room;
		for (var i = self.roomsIds.length; i--;) {
			if (room = roomsMapLogics.getRoom(self.roomsIds[i])) {
				break;
			}
		}
		return room;
	}
	this.getFloor = function() {
		return self.getRoom().getFloor();
	};
	this.getFloorNumber = function() {
		return self.getRoom().floorNumber;
	};
	this.getCategory = function() {
		return self.categoryId ? roomsMapLogics.getCategory(self.categoryId) : null;
	};
	init();
};

window.RoomsMapIcon = function(info) {
	var self = this;
	this.id = null;
	this.title = null;
	this.image = null;

	var init = function() {
		importInfo(info);
	};
	var importInfo = function(importedInfo) {
		self.id = importedInfo.id;
		self.title = importedInfo.title;
		self.image = importedInfo.image;
	};
	init();
};