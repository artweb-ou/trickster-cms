window.FloorMapControlsComponent = function(componentElement) {
	var roomSearchComponent;
	var categoriesSelectorComponent;
	var categoriesListComponent;
	var roomsListComponent;

	var init = function() {
		var element;
		if (element = _('.roomsmap_search_block', componentElement)[0]) {
			roomSearchComponent = new RoomsMapSearchComponent(element);
		}
		if (element = _('.roomsmap_category_selector_block', componentElement)[0]) {
			categoriesSelectorComponent = new RoomsMapCategoriesSelectorComponent(element);
		}

		if (element = _('.roomsmap_categorieslist', componentElement)[0]) {
			categoriesListComponent = new RoomsMapCategoriesListComponent(element);
		}
		var elements = _('.roomsmap_roomslist', componentElement);
		for (var i = 0; i < elements.length; i++) {
			roomsListComponent = new RoomsMapListComponent(elements[i]);
		}
	};
	init();
};

window.RoomsMapSearchComponent = function(componentElement) {
	var self = this;
	var inputElement;
	var submitElement;

	this.componentElement = null;

	var init = function() {
		createDomStructure();
	};
	var createDomStructure = function() {
		self.componentElement = componentElement;

		if (inputElement = _('.roomsmap_search_input')[0]) {
			window.eventsManager.addHandler(inputElement, 'change', inputChangeHandler);
		}

		if (submitElement = _('.roomsmap_search_submit')[0]) {
			window.eventsManager.addHandler(submitElement, 'click', submitClickHandler);
		}

		controller.addListener('roomsSearchQueryChanged', queryChangeHandler);
		controller.addListener('roomsCategoryChanged', categoryChangeHandler);

	};
	var inputChangeHandler = function() {
		var value = inputElement.value;
		document.location.href = '#search=' + value;
	};
	var submitClickHandler = function() {
		var value = inputElement.value;
		document.location.href = '#search=' + value;
	};
	var categoryChangeHandler = function() {
		inputElement.value = '';
	};
	var queryChangeHandler = function(query) {
		if (inputElement.value != query) {
			inputElement.value = query;
		}
	};

	init();
};
window.RoomsMapCategoriesSelectorComponent = function(componentElement) {
	var self = this;
	var selectorElement;
	var dropDownComponent;

	this.componentElement = null;

	var init = function() {
		createDomStructure();
		fillContents();
		dropDownComponent = dropDownManager.getDropDown(selectorElement, {'changeCallback': selectorChangeHandler});
		componentElement.appendChild(dropDownComponent.componentElement);

		controller.addListener('roomsMapStateChanged', parametersChangeHandler);
	};
	var createDomStructure = function() {
		self.componentElement = componentElement;
		selectorElement = _('.roomsmap_category_selector', componentElement)[0];
	};
	var fillContents = function() {
		var option = document.createElement('option');
		option.value = '';
		option.text = window.translationsLogics.get('roomsmap.allcategories');
		try {
			selectorElement.add(option, null);
		} catch(ex) {
			selectorElement.add(option);
		}

		var categoriesList = window.roomsMapLogics.getCategoriesList();
		for (var i = 0; i < categoriesList.length; i++) {
			var info = categoriesList[i];
			option = document.createElement('option');
			option.value = info.id;
			option.text = info.title;
			try {
				selectorElement.add(option, null);
			} catch(ex) {
				selectorElement.add(option);
			}
		}
	};
	var selectorChangeHandler = function() {
		var category = window.roomsMapLogics.getCategory(selectorElement.value);
		if (category) {
			document.location.href = category.getUrl();
		} else {
			document.location.href = '#';
		}
	};
	var parametersChangeHandler = function() {
		var categoryId = window.roomsMapLogics.getCurrentCategoryId();
		var query = window.roomsMapLogics.getCurrentQuery();
		dropDownComponent.setChangeCallback(false);
		dropDownComponent.setValue(categoryId, true);
		dropDownComponent.setChangeCallback(selectorChangeHandler);

		if (categoryId || query) {
			displayComponent();
		} else {
			hideComponent();
		}
	};
	var displayComponent = function() {
		componentElement.style.display = 'block';
	};
	var hideComponent = function() {
		componentElement.style.display = 'none';
	};
	init();
};

window.RoomsMapCategoriesListComponent = function(componentElement) {
	var self = this;
	var contentElement;
	this.componentElement = null;

	var init = function() {
		createDomStructure();
		fillContents();
		controller.addListener('roomsMapStateChanged', parametersChangeHandler);
	};
	var createDomStructure = function() {
		self.componentElement = componentElement;
		contentElement = _('.roomsmap_categorieslist_content', componentElement)[0];
	};
	var fillContents = function() {
		var categoriesList = window.roomsMapLogics.getCategoriesList();
		for (var i = 0; i < categoriesList.length; i++) {
			var category = new RoomsMapCategoriesListItemComponent(categoriesList[i]);
			contentElement.appendChild(category.componentElement);
		}
	};
	var parametersChangeHandler = function() {
		var categoryId = window.roomsMapLogics.getCurrentCategoryId();
		var query = window.roomsMapLogics.getCurrentQuery();
		if (categoryId || query) {
			hideComponent();
		} else {
			displayComponent();
		}
	};

	var displayComponent = function() {
		componentElement.style.display = 'block';
	};
	var hideComponent = function() {
		componentElement.style.display = 'none';
	};

	init();
};

window.RoomsMapCategoriesListItemComponent = function(info) {
	var self = this;
	var componentElement;

	this.componentElement = null;

	var init = function() {
		createDomStructure();
		fillContents();
	};
	var createDomStructure = function() {
		componentElement = document.createElement('a');
		componentElement.className = 'roomsmap_category red_block_category';
		self.componentElement = componentElement;
	};
	var fillContents = function() {
		self.componentElement.innerHTML = info.title;
		self.componentElement.href = info.getUrl();
	};

	init();
};

window.RoomsMapListComponent = function(componentElement) {
	var self = this;
	var floorComponents;
	var contentElement;

	this.componentElement = null;

	var init = function() {
		createDomStructure();
		controller.addListener('roomsMapStateChanged', parametersChangeHandler);
		controller.addListener('roomsListRecalculated', listRecalculateHandler);
	};
	var createDomStructure = function() {
		self.componentElement = componentElement;
		contentElement = _('.roomsmap_roomslist_content', componentElement)[0];

		floorComponents = [];
		var floorsList = window.roomsMapLogics.getFloorsList();
		for (var i = 0; i < floorsList.length; i++) {
			var floor = new RoomsMapListFloorComponent(floorsList[i]);
			floorComponents.push(floor);
			contentElement.appendChild(floor.componentElement);
		}
	};
	var parametersChangeHandler = function() {
		var categoryId = window.roomsMapLogics.getCurrentCategoryId();
		var query = window.roomsMapLogics.getCurrentQuery();
		if (categoryId || query) {
			displayComponent();
		} else {
			hideComponent();
		}
	};
	var listRecalculateHandler = function() {
		var shopsList = window.roomsMapLogics.getShopsList();
		var index = {};
		for (var i = 0; i < shopsList.length; i++) {
			var floorId = shopsList[i].getFloor().number;
			if (!index[floorId]) {
				index[floorId] = [];
			}
			index[floorId].push(shopsList[i]);
		}
		for (var i = 0; i < floorComponents.length; i++) {
			var floor = floorComponents[i];
			var floorRooms = index[floor.number] || [];
			floor.setRoomsList(floorRooms);
		}
	};
	var displayComponent = function() {
		componentElement.style.display = 'block';
	};
	var hideComponent = function() {
		componentElement.style.display = 'none';
	};

	init();
};

window.RoomsMapListFloorComponent = function(info) {
	var self = this;
	var componentElement;
	var titleElement;
	var roomsElement;

	this.componentElement = null;
	this.number = null;
	this.id = null;

	var init = function() {
		self.number = info.number;
		self.id = info.id;
		createDomStructure();
		fillContents();

		controller.addListener('roomsMapStateChanged', parametersChangeHandler);
	};
	var createDomStructure = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'roomsmap_floorinfo';
		self.componentElement = componentElement;

		titleElement = document.createElement('a');
		titleElement.className = 'roomsmap_floorinfo_title';
		componentElement.appendChild(titleElement);

		roomsElement = document.createElement('div');
		roomsElement.className = 'roomsmap_floorinfo_rooms';
		componentElement.appendChild(roomsElement);
	};
	var fillContents = function() {
		titleElement.innerHTML = info.title;
		titleElement.href = info.url;
	};
	this.setRoomsList = function(roomsList) {
		if (roomsList.length > 0) {
			while (roomsElement.firstChild) {
				roomsElement.removeChild(roomsElement.firstChild);
			}
			for (var i = 0; i < roomsList.length; i++) {
				var room = new RoomsMapListItemComponent(roomsList[i], self.number);
				roomsElement.appendChild(room.componentElement);
			}
			displayComponent();
		} else {
			hideComponent();
		}
	};
	var parametersChangeHandler = function() {
		titleElement.href = info.url;
	};
	var displayComponent = function() {
		componentElement.style.display = 'block';
	};
	var hideComponent = function() {
		componentElement.style.display = 'none';
	};
	init();
};

window.RoomsMapListItemComponent = function(info, floor) {
	var self = this;
	var componentElement;

	this.componentElement = null;

	var init = function() {
		createDomStructure();
		fillContents();
	};
	var createDomStructure = function() {
		componentElement = document.createElement('a');
		componentElement.className = 'roomsmap_room red_block_shop';
		self.componentElement = componentElement;
	};
	var fillContents = function() {
		self.componentElement.innerHTML = info.title;
		self.componentElement.href = info.getShopUrl();
	};

	init();
};