window.RoomsMapComponent = function(componentElement) {
	var self = this;
	var roomInfoComponent;
	var roomsFloorSelectorComponent;
	var roomsFloorMapsComponent;
	var svgElement;
	var infoHeight = 200;
	var iconsHeight = 98;
	var minHeight = 512;
	var floorsContainerElement;

	var init = function() {
		var element, elements, i, roomsMapFloor;
		element = _('.roomsmap_views')[0];
		if (element) {
			element.style.display = 'block';
		}
		floorsContainerElement = _('.roomsmap_floors', componentElement)[0];

		if (element = _('.roomsmap_search_block', componentElement)[0]) {
			roomSearchComponent = new RoomsMapSearchComponent(element);
		}

		if (element = _('.roomsmap_category_selector_block', componentElement)[0]) {
			categoriesSelectorComponent = new RoomsMapCategoriesSelectorComponent(element);
		}

		if (element = _('.roomsmap_categorieslist', componentElement)[0]) {
			categoriesListComponent = new RoomsMapCategoriesListComponent(element);
		}
		elements = _('.roomsmap_roomslist', componentElement);
		for (i = 0; i < elements.length; i++) {
			roomsListComponent = new RoomsMapListComponent(elements[i]);
		}
		if (element = _('.roomsmap_roominfo', componentElement)[0]) {
			roomInfoComponent = new RoomsMapInfoComponent(element);
		}
		if (element = _('.roomsmap_floors_selector', componentElement)[0]) {
			roomsFloorSelectorComponent = new RoomsMapFloorSelectorComponent(element);
		}
		if (element = _('.roomsmap_floors', componentElement)[0]) {
			roomsFloorMapsComponent = new RoomsMapFloorMapsComponent(element);
		}
		if (element = _('.roomsmap_floor', componentElement)[0]) {
			roomsMapFloor = element;
		}
		if (element = roomsMapFloor.querySelector('svg')) {
			svgElement = element;
		}

		self.registerScalableElement({
			'scaledElement': roomsMapFloor,
			'gestureElement': roomsMapFloor,
			'positionedElement': roomsMapFloor,
			'minWidth': 290,
			'minHeight': 290,
			'maxWidth': 1000,
			'maxHeight': 1000
		});
		self.registerDraggableElement({
			'draggableElement': roomsMapFloor,
			'parentElement': roomsFloorMapsComponent.getComponentElement(),
			'gestureElement': roomsFloorMapsComponent.getComponentElement(),
			'boundariesElement': roomsFloorMapsComponent.getComponentElement(),
			'boundariesPadding': 0.5
		});
	};

	var indexClick = function(event) {
		if (event.target && event.target.className == 'shop_line_title') {
			TweenLite.to(window, 0.5, {'scrollTo': 0});
		}
	};
	init();
};

ScalableComponent.call(RoomsMapComponent.prototype);
DraggableComponent.call(RoomsMapComponent.prototype);

window.RoomsMapInfoComponent = function(componentElement) {
	var contentElement;

	var logoElement;
	var logoWrapElement;
	var imageWrapElement;
	var imageElement;
	var titleElement;
	var textElement;
	var openedTimeElement;
	var contactInfoElement;
	var buttonElement;
	var closeButtonElement;
	var campaignsElement;
	var detailsElement;
	var visible = false;

	var init = function() {
		createDomStructure();

		document.body.appendChild(componentElement);

		window.eventsManager.addHandler(window, 'resize', setPosition);
		window.eventsManager.addHandler(closeButtonElement, 'click', closePopUp);
		window.eventsManager.addHandler(componentElement, 'click', clickHandler);

		controller.addListener('roomChanged', roomChangeHandler);
	};
	var clickHandler = function(event) {
		window.eventsManager.cancelBubbling(event);
	};

	var createDomStructure = function() {
		contentElement = _('.roomsmap_roominfo_content', componentElement)[0];
		closeButtonElement = _('.roomsmap_roominfo_close_button', componentElement)[0];
		logoWrapElement = _('.shop_short_logo_wrap', contentElement)[0];
		imageWrapElement = _('.shop_short_image_container', contentElement)[0];
		titleElement = _('.shop_short_title', contentElement)[0];
		textElement = _('.shop_short_text', contentElement)[0];
		openedTimeElement = _('.shop_short_openedtime', contentElement)[0];
		contactInfoElement = _('.shop_short_contactinfo', contentElement)[0];
		buttonElement = _('.shop_short_readmore_button', contentElement)[0];
		detailsElement = _('.shop_short_details', componentElement)[0];

		campaignsElement = _('.shop_short_campaigns', contentElement)[0];
	};

	var closePopUp = function() {
		if (visible) {
			hide();
		}
	};
	var roomChangeHandler = function(id) {
		var shopInfo = null;
		var roomInfo = null;
		if (id) {
			roomInfo = roomsMapLogics.getRoom(id);
		}
		if (roomInfo) {
			shopInfo = roomInfo.getShop();
		}
		if (shopInfo) {
			display();
			titleElement.innerHTML = shopInfo.title;
			textElement.innerHTML = shopInfo.introduction;
			openedTimeElement.innerHTML = '';
			if (shopInfo.openedTime || shopInfo.openingHoursInfo.length || shopInfo.contactInfo || shopInfo.logo) {
				if (shopInfo.openingHoursInfo.length) {
					for (var i = 0; i < shopInfo.openingHoursInfo.length; ++i) {
						var info = shopInfo.openingHoursInfo[i];
						var lineElement = document.createElement('p');
						lineElement.innerHTML = info.name + ' ' + info.times;
						openedTimeElement.appendChild(lineElement);
					}
				}
				else if (shopInfo.openedTime) {
					openedTimeElement.innerHTML = shopInfo.openedTime;
				}
				contactInfoElement.innerHTML = shopInfo.contactInfo;
				detailsElement.style.display = 'block';
			}
			else {
				detailsElement.style.display = 'none';
			}
			buttonElement.href = shopInfo.URL;

			if (imageElement) {
				imageWrapElement.removeChild(imageElement);
				imageElement = null;
			}
			var image = shopInfo.image || window.shopDefaultImage || '';
			if (image) {
				imageElement = document.createElement('img');
				imageElement.className = 'shop_short_image';
				if (shopInfo.image) {
					imageElement.src = shopInfo.image;
				} else if (window.shopDefaultImage) {
					imageElement.src = window.shopDefaultImage;
				}
				imageWrapElement.appendChild(imageElement);
				imageWrapElement.style.display = 'block';
			} else {
				imageWrapElement.style.display = 'none';
			}
			if (logoElement) {
				logoWrapElement.removeChild(logoElement);
				logoElement = null;
			}
			if (shopInfo.logo) {
				logoElement = document.createElement('div');
				logoElement.className = 'shop_short_logo';
				logoElement.style.backgroundImage = 'url("' + shopInfo.logo + '")';
				logoWrapElement.appendChild(logoElement);
				logoWrapElement.style.display = 'block';
			} else {
				logoWrapElement.style.display = 'none';
			}
			while (campaignsElement.firstChild) {
				campaignsElement.removeChild(campaignsElement.firstChild);
			}
			if (shopInfo.campaigns) {
				for (var i = 0; i < shopInfo.campaigns.length; ++i) {
					var campaign = new RoomsMapInfoCampaignComponent(shopInfo.campaigns[i]);
					campaignsElement.appendChild(campaign.getComponentElement());
				}
			}
			contentElement.style.display = 'block';
			setPosition();
		} else {
			hide();
		}
	};

	var display = function() {
		visible = true;
		componentElement.style.display = 'block';
		window.eventsManager.addHandler(window, 'click', windowClick);
	};

	var hide = function() {
		visible = false;
		componentElement.style.display = 'none';
		window.eventsManager.removeHandler(window, 'click', windowClick);
	};

	var windowClick = function(event) {
		closePopUp();
	};

	var setPosition = function() {
		componentElement.style.left = (getWindowWidth() - componentElement.offsetWidth) / 2 + 'px';
	};

	var getWindowWidth = function() {
		return window.innerWidth ? window.innerWidth : document.documentElement.offsetWidth;
	};

	init();
};

window.RoomsMapInfoCampaignComponent = function(info) {
	var componentElement;
	var self = this;

	var init = function() {
		componentElement = self.makeElement('div', 'campaign_bar');
		var innerElement = self.makeElement('div', 'campaign_bar_inner', componentElement);
		if (info.image) {
			var imageContainerElement = self.makeElement('div', 'campaign_bar_image_wrap', innerElement);
			self.makeElement('img', {'src': info.image, 'className': 'campaign_bar_image'}, imageContainerElement);
		}
		var contentElement = self.makeElement('div', 'campaign_bar_content', innerElement);
		var titleElement = self.makeElement('h3', 'campaign_bar_title', contentElement);
		titleElement.innerHTML = info.title;
		var descriptionElement = self.makeElement('div', 'campaign_bar_description', contentElement);
		descriptionElement.innerHTML = info.content;
		self.makeElement('div', 'clearfix', innerElement);
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
DomElementMakerMixin.call(RoomsMapInfoCampaignComponent.prototype);

window.RoomsMapFloorSelectorComponent = function(componentElement) {
	var floors = [];

	var init = function() {
		createDomStructure();
	};
	var createDomStructure = function() {
		var floorsList = roomsMapLogics.getFloorsList();
		for (var i = 0; i < floorsList.length; i++) {
			var floor = new RoomsFloorSelectorItemComponent(floorsList[i]);
			componentElement.appendChild(floor.componentElement);
			floors.push(floor);
		}
	};
	init();
};
window.RoomsFloorSelectorItemComponent = function(floorInfo) {
	var self = this;
	var componentElement;
	var textElement;
	var selected = false;

	this.componentElement = null;
	var init = function() {
		createDomStructure();
		controller.addListener('roomsMapStateChanged', parametersChangeHandler);
	};
	var createDomStructure = function() {
		componentElement = document.createElement('a');
		componentElement.href = floorInfo.url;
		componentElement.className = 'roomsmap_floors_selector_item';
		self.componentElement = componentElement;

		textElement = document.createElement('div');
		textElement.className = 'roomsmap_floors_selector_item_title';
		textElement.innerHTML = floorInfo.title;
		componentElement.appendChild(textElement);
	};
	var parametersChangeHandler = function() {
		var floor = window.roomsMapLogics.getCurrentFloorNumber();
		if (floor == floorInfo.number) {
			selected = true;
		} else {
			selected = false;
		}
		componentElement.href = floorInfo.url;
		refreshComponent();
	};
	var refreshComponent = function() {
		if (selected) {
			domHelper.addClass(componentElement, 'selected');
		} else {
			domHelper.removeClass(componentElement, 'selected');
		}
	};
	init();
};

window.RoomsMapFloorMapsComponent = function(componentElement) {
	var self = this;
	var floors = [];

	var init = function() {
		createDomStructure();
	};
	var createDomStructure = function() {
		var floorsList = roomsMapLogics.getFloorsList();
		for (var i = 0; i < floorsList.length; i++) {
			var floor = new RoomsMapFloorMapComponent(floorsList[i]);
			componentElement.appendChild(floor.componentElement);
			floors.push(floor);
		}
	};
	this.setSizes = function(width, height) {
		for (var i = 0; i < floors.length; i++) {
			floors[i].setSizes(width, height);
		}
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	this.getScaledComponentElement = function() {
		return componentElement.querySelector('.svg');
	};

	init();
};

window.RoomsMapFloorMapComponent = function(info) {
	var self = this;
	var vectorsInfo = {};
	var componentElement;

	var paper;
	var allElements;
	var label;
	var padding = 0;
	var maxWidth = 0;
	var maxHeight = 0;
	var viewBoxWidth;
	var viewBoxHeight;
	var svg;
	var roomContainer;

	this.componentElement = null;

	var init = function() {
		vectorsInfo = info.mapInfo;
		roomContainer = document.querySelector('.roomsmap_block');

		if (vectorsInfo.dimensions) {
			viewBoxWidth = vectorsInfo.dimensions.width;
			viewBoxHeight = vectorsInfo.dimensions.height;
		} else {
			getWidthHeigt();
		}
		createDomStructure();
		controller.addListener('roomsMapStateChanged', parametersChangeHandler);

		window.addEventListener('resize', changeSvgViewBox );
	};
	var getWidthHeigt = function() {
		viewBoxWidth = roomContainer.clientWidth;
		viewBoxHeight = roomContainer.clientWidth;
	};
	var changeSvgViewBox = function() {

	};
	var createDomStructure = function() {
		componentElement = document.createElement('div');
		componentElement.className = 'roomsmap_floor';
		self.componentElement = componentElement;
		svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
		svg.setAttribute('viewBox', '0 0 1000 1000');
		var roomsInfo = vectorsInfo.rooms;
		for (var i = 0; i < roomsInfo.length; ++i) {
			if (roomsInfo[i].id == 'background') {
				var bg = document.createElementNS("http://www.w3.org/2000/svg", 'rect');
				bg.setAttribute("d", roomsInfo[i].walls.top[0]);
				bg.setAttribute("fill", '#cccccc');
				bg.setAttribute("stroke-width", '1');
				bg.setAttribute("opacity", '0');
				svg.appendChild(bg);
			} else {
				new RoomsMapFloorRoomComponent(roomsInfo[i], svg, info.number);
			}
		}
		var mapIcons = vectorsInfo.icons;
		if (mapIcons) {
			for (var i = mapIcons.length; i--;) {
				var mapIcon = mapIcons[i];
				var source = '';
				if (mapIcon['href']) {
					// SVG export
					source = mapIcon['href'];
				} else if (mapIcon['code']) {
					// CMS icon
					var iconInfo = roomsMapLogics.getIcon(mapIcon['code']);
					if (iconInfo) {
						source = iconInfo.image;
					}
				}
				if (source) {
					var image = document.createElement('image');
					image.setAttribute("x", mapIcon.x);
					image.setAttribute("y", mapIcon.y);
					image.setAttribute("width", mapIcon.width);
					image.setAttribute("height", mapIcon.height);
					image.setAttribute("href", source);
					image.setAttribute("pointer-events", "none");

				}
				svg.appendChild(image);
			}
		}
		componentElement.appendChild(svg);
		label = new RoomsMapFloorLabelComponent(componentElement, viewBoxWidth, viewBoxHeight, svg, info.number);
	};
	var parametersChangeHandler = function() {
		var floor = window.roomsMapLogics.getCurrentFloorNumber();
		if (floor == info.number) {
			displayComponent();
		} else {
			hideComponent();
		}
	};
	var displayComponent = function() {
		componentElement.style.display = 'block';
		alignSizes();
		label.displayComponent();
	};
	var hideComponent = function() {
		componentElement.style.display = 'none';
		label.hideComponent();
	};

	this.setSizes = function(newMaxWidth, newMaxHeight) {
		maxWidth = newMaxWidth;
		maxHeight = newMaxHeight;
		alignSizes();
	};

	var alignSizes = function() {
		var sizes = calculateSizes();

		var left = Math.round((maxWidth - sizes.width) / 2);
		var top = Math.round((maxHeight - sizes.height) / 2);
		componentElement.style.left = left + 'px';
		componentElement.style.top = top + 'px';

		//ignore svg viewbox change for zooming, scaling
		// paper.setSize(sizes.width, sizes.height);
		var positions = domHelper.getElementPositions(componentElement);
		positions.x += Math.max(componentElement.offsetWidth - sizes.width, 1) / 2;
		if (label) {
			label.setSize(positions.x, positions.y, sizes.width, sizes.height);
		}
	};
	var calculateSizes = function() {
		var paddedWidth = maxWidth - padding * 2;
		var paddedHeight = maxHeight - padding * 2;
		var width = paddedWidth;
		var height = Math.round(viewBoxHeight * paddedWidth / viewBoxWidth);

		if (height > paddedHeight) {
			height = paddedHeight;
			width = Math.round(viewBoxWidth * paddedHeight / viewBoxHeight);
		}
		return {'width': width, 'height': height};
	};

	init();
};
window.RoomsMapFloorRoomComponent = function(vectorInfo, svg, floor) {
	var roomInfo;
	var shopInfo;
	var tipContentComponent;
	var tipComponent;
	var path;
	var color;
	var stroke;

	var init = function() {
		getShopInfo(vectorInfo.id);
		createShape();
		createRoomTip();
		tipContentComponent.innerHTML = shopInfo.title;
		setEvents();

	};
	var getShopInfo = function(id) {
		if (String(id).indexOf('_') >= 0) {
			// info came from SVG export
			roomInfo = roomsMapLogics.getRoomByExportId(id);
			if (roomInfo) {
				shopInfo = roomInfo.getShop();
			} else {
				// room is not entered in admin
			}
		} else {
			roomInfo = roomsMapLogics.getRoom(id);
			shopInfo = roomsMapLogics.getShopByRoomId(id);
		}
	};

	var createShape = function() {
		var category = shopInfo.getCategory();
		if (category && category.color) {
			stroke = category.colorStroke;
			color = category.colorTop;
		}
		path = document.createElementNS("http://www.w3.org/2000/svg", 'path');
		path.setAttribute("d",vectorInfo.walls.top[0]);
		path.setAttribute("id", vectorInfo.id);
		path.setAttribute("fill", color);
		path.setAttribute("stroke", stroke);
		svg.appendChild(path);
	};

	var setEvents = function() {
		path.addEventListener('mouseenter', mouseEnterHandler);
		path.addEventListener('mouseleave', mouseLeaveHandler);
		path.addEventListener('mousemove', mouseMoveHandler);
		path.addEventListener('click', clickHandler);
		// path.addEventListener('pointerdown', clickHandler);
		// path.addEventListener('touchstart', clickHandler);
	};

	var createRoomTip = function() {
		var content = document.querySelector('.center_column');
		tipComponent = document.createElement('div');
		tipComponent.className = 'tip_popup';
		tipComponent.style.position = 'fixed';
		tipComponent.style.visibility = 'hidden';
		tipContentComponent = document.createElement('div');
		tipContentComponent.className = 'tip_popup_content';
		tipComponent.appendChild(tipContentComponent);
		content.appendChild(tipComponent);
	};

	var mouseEnterHandler = function() {
		tipComponent.style.visibility = 'visible';
		path.setAttribute("fill", calculateColor(color, 0.40, false));
	};

	var mouseMoveHandler = function(event) {
		tipComponent.style.left = event.clientX+'px';
		tipComponent.style.top = event.clientY-50+'px';
	};

	var mouseLeaveHandler = function() {
		tipComponent.style.visibility = 'hidden';
		path.setAttribute("fill", color);
	};

	var clickHandler = function(event) {
		controller.fireEvent('roomChanged', shopInfo.id);
		document.location.href = roomInfo.getRoomUrl(floor);
		eventsManager.cancelBubbling(event);
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

	var pad = function(num, totalChars) {
		var pad = '0';
		num = num + '';
		while (num.length < totalChars) {
			num = pad + num;
		}
		return num;
	};

	init();
};

window.RoomsMapFloorLabelComponent = function(parentElement, viewBoxWidth, viewBoxHeight, paper, floorNumber) {
	var popupComponent;

	var parentX;
	var parentY;

	var parentWidth;
	var parentHeight;

	var currentSelectedVector;
	var currentOverRoom;
	var currentRoomTimeout;

	var init = function() {
		popupComponent = new ToolTipComponent({
			'beforeDisplay': toolTipDisplayCheck,
			'referralElement': document.body,
			'hideOnClick': false,
			'hideOnLeave': false
		});
		controller.addListener('roomChanged', roomChangeHandler);
		controller.addListener('roomComponentMouseOver', roomComponentMouseOver);
		controller.addListener('roomComponentMouseOut', roomComponentMouseOut);
		controller.addListener('roomComponentSelected', roomComponentSelected);
	};
	var toolTipDisplayCheck = function() {
		return floorNumber == window.roomsMapLogics.getCurrentFloorNumber();
	};
	var roomChangeHandler = function(id) {
		if (!id) {
			currentSelectedVector = null;
			popupComponent.setText('');
			popupComponent.hideComponent(mouseOutCallback);
		}
	};
	this.hideComponent = function() {
		popupComponent.hideComponent();
	};
	this.displayComponent = function() {
		displayCurrentRoom();
	};
	var roomComponentSelected = function(info) {
		popupComponent.setText('');
		popupComponent.hideComponent(mouseOutCallback);
		if (floorNumber == info.floor) {
			currentSelectedVector = info.vector;
			var currentFloorNumber = roomsMapLogics.getCurrentFloorNumber();

			if (currentFloorNumber == floorNumber) {
				displayCurrentRoom();
			}
		} else {
			currentSelectedVector = null;
		}
	};
	var roomComponentMouseOver = function(info) {
		if (floorNumber == info.floor) {
			window.clearTimeout(currentRoomTimeout);
			currentOverRoom = info;
			var shopName = '';
			if (info.room) {
				var shopInfo = info.room.getShop();
				if (shopInfo) {
					shopName = shopInfo.title;
				}
			}
			popupComponent.changeBehaviour('mouseover');
			popupComponent.setFixedCoordinates(false, false);
			popupComponent.setText(shopName);
			popupComponent.displayComponent();
		}
	};
	var roomComponentMouseOut = function(info) {
		if (floorNumber == info.floor) {
			currentOverRoom = null;
			popupComponent.setText('');
			popupComponent.hideComponent(mouseOutCallback);
		}
	};
	var mouseOutCallback = function() {
		if (!currentOverRoom) {
			currentRoomTimeout = window.setTimeout(displayCurrentRoom, 500);
		}
	};
	var displayCurrentRoom = function() {
		if (currentSelectedVector) {
			var currentSelectedRoom = window.roomsMapLogics.getCurrentRoom();
			if (!currentOverRoom || currentOverRoom.id != currentSelectedRoom.id) {
				if (floorNumber == window.roomsMapLogics.getCurrentFloorNumber()) {
					var shopName = '';
					var shop = currentSelectedRoom.getShop();
					if (shop) {
						shopName = shop.title;
					}
					popupComponent.setText(shopName);
					var vectorInfo = currentSelectedVector.getBBox();

					var coeffX = viewBoxWidth / parentWidth;
					var coeffY = viewBoxHeight / parentHeight;

					var vectorX = vectorInfo.x + vectorInfo.width / 2;
					var vectorY = vectorInfo.y + vectorInfo.height / 2;

					var x = parentX + vectorX / coeffX;
					var y = parentY + vectorY / coeffY;

					popupComponent.setFixedCoordinates(x, y);
					popupComponent.changeBehaviour('static');
					popupComponent.displayComponent();
				}
			}
		}
	};
	this.setSize = function(newParentX, newParentY, newWidth, newHeight) {
		parentWidth = newWidth;
		parentHeight = newHeight;

		parentX = newParentX;
		parentY = newParentY;
		displayCurrentRoom();
	};

	init();
};