window.FloorMapComponent = function(componentElement) {
	var self = this;

	this.svgNS = 'http://www.w3.org/2000/svg';
	this.xlinkNS = 'http://www.w3.org/1999/xlink';
	this.regionsList = [];
	this.regionsIndex = [];
	this.newRegionPoints = [];
	this.newIcons = [];
	this.svgMouseTracker = false;

	this.svgElement = false;
	var iconImages = {};
	var previewElement;
	var previewPolygon;

	var init = function() {
		createDomStructure();
		controller.addListener('editingModeChanged', editingModeChanged);
		controller.addListener('editingCompleted', self.editingCompletedHandler);
		controller.addListener('editingCancelled', self.editingCancelledHandler);
		window.eventsManager.addHandler(self.svgElement, 'click', clickHandler);
		window.eventsManager.addHandler(componentElement, 'mousemove', mousemoveHandler);
		window.eventsManager.addHandler(componentElement, 'mouseout', mouseoutHandler);
		window.eventsManager.addHandler(window, 'resize', resizeHandler);
		self.planInformation = window.editorInfo;
		self.updateData();
	}
	var mousemoveHandler = function(event) {
		var editingMode = window.floorMapLogics.getEditingMode();
		if (editingMode) {
			var postion = self.getNewRegionPointPosition(event.shiftKey);

			var icon = floorMapLogics.getSelectedIcon();
			if (icon) {
				var m = self.svgElement.getScreenCTM();
				var p = self.svgElement.createSVGPoint();
				p.x = self.svgMouseTracker.mouseX;
				p.y = self.svgMouseTracker.mouseY;
				p = p.matrixTransform(m.inverse());

				var panel = floorMapLogics.getPanel();
				var width = panel.getIconWidth() || 50;
				var height = panel.getIconHeight() || 50;
				var angle = panel.getIconAngle();

				if (!previewElement) {
					var iconInfo = floorMapLogics.getSelectedIcon();
					previewElement = self.createIconImage(iconInfo, p.x, p.y, width, height);
					previewElement.setRotation(angle);
				}
				else {
					previewElement.setWidth(width);
					previewElement.setHeight(height);
					previewElement.setLocation(p.x, p.y);
					previewElement.setRotation(angle);
				}

			} else {
				if (!previewElement) {
					previewElement = new regionPointComponent(postion.x, postion.y);
					previewElement.setColor('#FF00FE');
					self.svgElement.appendChild(previewElement.componentElement);
				}
				else {
					previewElement.setLocation(postion.x, postion.y);
				}
				if (self.newRegionPoints.length > 0) {
					if (!previewPolygon) {
						previewPolygon = new FloorMapPreviewPolygon();
						self.svgElement.appendChild(previewPolygon.getComponentElement());
					}
					var previewPoints = self.newRegionPoints.slice();
					previewPoints.push(postion);
					previewPolygon.setPoints(previewPoints);
				}
			}
		}
	};
	var mouseoutHandler = function(event) {
		var targetElement = event.relatedTarget || event.toElement;
		if (targetElement && targetElement.parentNode == self.svgElement) {
			return;
		}
		if (previewElement) {
			self.svgElement.removeChild(previewElement.componentElement);
			previewElement = null;
		}
		if (previewPolygon) {
			var previewPoints = self.newRegionPoints.slice();
			previewPolygon.setPoints(previewPoints);
		}
	};
	var createDomStructure = function() {
		adjustHeight();

		var svgElement = document.createElementNS(self.svgNS, 'svg:svg');

		svgElement.setAttribute('width', '100%');
		svgElement.setAttribute('height', '100%');
		svgElement.setAttribute('viewBox', '0 0 1000 1000');
		if (window.editorInfo.image != '') {
			var image = document.createElementNS(self.svgNS, 'svg:image');
			image.setAttribute('x', '0');
			image.setAttribute('y', '0');
			image.setAttribute('width', '1000');
			image.setAttribute('height', '1000');
			image.setAttributeNS(self.xlinkNS, 'href', window.editorInfo.image);

			svgElement.appendChild(image);
		}
		componentElement.appendChild(svgElement);

		self.svgElement = svgElement;

		self.svgMouseTracker = new window.customMouseTracker(self.svgElement);
	}
	var resizeHandler = function() {
		adjustHeight();
	};
	var adjustHeight = function() {
		componentElement.style.width = '';
		componentElement.style.height = '';
		componentElement.style.width = componentElement.parentNode.offsetHeight + 'px';
		componentElement.style.height = componentElement.parentNode.offsetHeight + 'px';
	};
	this.updateData = function() {
		this.buildSectionRegions();
		var icons = window.floorMapLogics.getIconsList();
		for (var i = icons.length; i--;) {
			var iconInfo = icons[i];
			for (var j = iconInfo.nodes.length; j--;) {
				var node = iconInfo.nodes[j];
				var iconImage = self.createIconImage(iconInfo, node.x, node.y, node.width, node.height);
				if (typeof iconImages[iconInfo.id] == 'undefined') {
					iconImages[iconInfo.id] = [];
				}
				iconImages[iconInfo.id].push(iconImage);
				if (node.rotation) {
					iconImage.setRotation(node.rotation);
				}
			}
		}
	}
	this.buildSectionRegions = function() {
		var sectionsList = window.floorMapLogics.getRoomsList();
		for (var i = 0; i < sectionsList.length; i++) {
			var sectionRegion = new sectionRegionComponent(sectionsList[i]);
			self.regionsList.push(sectionRegion);
			self.regionsIndex[sectionRegion.id] = sectionRegion;
			self.svgElement.appendChild(sectionRegion.componentElement);
		}
	}
	var editingModeChanged = function() {
		var editingMode = window.floorMapLogics.getEditingMode();
		if (editingMode) {
			self.newRegionPoints = [];
			componentElement.style.cursor = 'none';
		}
		else {
			componentElement.style.cursor = '';
		}
	}
	var clickHandler = function(event) {
		eventsManager.preventDefaultAction(event);
		var editingMode = window.floorMapLogics.getEditingMode();
		if (editingMode) {
			var icon = floorMapLogics.getSelectedIcon();
			if (icon) {
				var m = self.svgElement.getScreenCTM();
				var p = self.svgElement.createSVGPoint();
				p.x = self.svgMouseTracker.mouseX;
				p.y = self.svgMouseTracker.mouseY;
				p = p.matrixTransform(m.inverse());
				var iconInfo = floorMapLogics.getSelectedIcon();
				var panel = floorMapLogics.getPanel();
				var width = panel.getIconWidth() || 50;
				var height = panel.getIconHeight() || 50;
				var iconImage = self.createIconImage(iconInfo, p.x, p.y, width, height);
				var angle = panel.getIconAngle();
				iconImage.setRotation(angle);
				self.newIcons.push(iconImage);
			}
			else {
				var p = self.getNewRegionPointPosition(event.shiftKey);
				self.createRegionPoint(p.x, p.y)

				var afterData = {};
				afterData['x'] = p.x;
				afterData['y'] = p.y;
				afterData['number'] = self.newRegionPoints.length - 1;

				window.undoManagerLogics.registerAction(self, 'createRegionPoint', {}, afterData);
			}
		}
		if (previewElement) {
			self.svgElement.removeChild(previewElement.componentElement);
			previewElement = null;
		}
	}
	this.getNewRegionPointPosition = function(shiftPressed) {
		var p = self.getCursorSvgPosition();
		var panel = floorMapLogics.getPanel();
		var precision = panel.getPrecision();
		if (precision > 0) {
			p.x = Math.round(p.x / precision) * precision;
			p.y = Math.round(p.y / precision) * precision;
		}
		if (shiftPressed && self.newRegionPoints.length > 0) {
			var lastRegionPoint = self.newRegionPoints[self.newRegionPoints.length - 1];
			var diffX = Math.abs(p.x - lastRegionPoint.x);
			var diffY = Math.abs(p.y - lastRegionPoint.y);
			if (diffX < diffY) {
				p.x = lastRegionPoint.x;
			} else {
				p.y = lastRegionPoint.y;
			}
		}
		return p;
	};
	this.getCursorSvgPosition = function() {
		var m = self.svgElement.getScreenCTM();
		var p = self.svgElement.createSVGPoint();
		p.x = self.svgMouseTracker.mouseX;
		p.y = self.svgMouseTracker.mouseY;
		p = p.matrixTransform(m.inverse());
		return p;
	};
	this.createRegionPoint = function(x, y) {
		var regionPoint = new regionPointComponent(x, y);

		self.svgElement.appendChild(regionPoint.componentElement);
		self.newRegionPoints.push(regionPoint);
	}
	this.createIconImage = function(iconInfo, x, y, width, height) {
		var icon = new FloorMapIconImageComponent(iconInfo, x, y, width, height);
		self.svgElement.appendChild(icon.getComponentElement());
		return icon;
	}
	this.editingCompletedHandler = function() {
		if (previewElement) {
			self.svgElement.removeChild(previewElement.componentElement);
			previewElement = null;
		}
		if (previewPolygon) {
			self.svgElement.removeChild(previewPolygon.getComponentElement());
			previewPolygon = null;
		}
		if (self.newRegionPoints.length) {
			var nodes = [];
			for (var i = 0; i < self.newRegionPoints.length; i++) {
				var node = {x: self.newRegionPoints[i].x, y: self.newRegionPoints[i].y, number: i};
				nodes.push(node);
			}
			window.floorMapLogics.persistRoom(nodes);
			self.clearRegionPoints();
		}
		else if (self.newIcons.length) {
			var selectedIconId = floorMapLogics.getSelectedIcon().id;
			if (typeof iconImages[selectedIconId] == 'undefined') {
				iconImages[selectedIconId] = [];
			}
			for (var i = 0; i < self.newIcons.length; i++) {
				iconImages[selectedIconId].push(self.newIcons[i]);
			}
			window.floorMapLogics.persistIcons(iconImages[floorMapLogics.getSelectedIcon().id]);
			self.newIcons = [];
		}
		if (previewElement) {
			self.svgElement.removeChild(previewElement.componentElement);
			previewElement = null;
		}
	}
	this.editingCancelledHandler = function() {
		self.clearRegionPoints();
		self.clearNewIcons();
		if (previewElement) {
			self.svgElement.removeChild(previewElement.componentElement);
			previewElement = null;
		}
	}
	this.clearRegionPoints = function() {
		for (var i = 0; i < self.newRegionPoints.length; i++) {
			self.svgElement.removeChild(self.newRegionPoints[i].componentElement);
		}
		self.newRegionPoints = [];
	}
	this.clearNewIcons = function() {
		for (var i = 0; i < self.newIcons.length; i++) {
			self.svgElement.removeChild(self.newIcons[i].getComponentElement());
		}
		self.newIcons = [];
	}
	this.performRedo = function(actionType, beforeData, afterData) {
		if (actionType == 'createRegionPoint') {
			self.createRegionPoint(afterData.x, afterData.y);
		}
	}
	this.performUndo = function(actionType, beforeData, afterData) {
		if (actionType == 'createRegionPoint') {
			if (self.regionsList[afterData.number]) {
				var point = self.newRegionPoints[afterData.number];
				self.newRegionPoints.splice(afterData.number, 1);
				self.svgElement.removeChild(point.componentElement);
			}
			if (previewPolygon) {
				if (self.newRegionPoints.length > 1) {
					var previewPoints = self.newRegionPoints.slice();
					previewPolygon.setPoints(previewPoints);
				}
				else {
					self.svgElement.removeChild(previewPolygon.getComponentElement());
					previewPolygon = null;
				}
			}
		}
		else if (actionType == ' addNewIconImage') {
			if (self.newIcons.length) {
				var lastIconImage = self.newIcons.pop();
				self.svgElement.removeChild(lastIconImage.getComponentElement());
			}
		}
	}
	this.getIconImages = function() {
		return iconImages;
	}
	init();
};
DomHelperMixin.call(FloorMapComponent.prototype);

window.FloorMapIconImageComponent = function(info, x, y, width, height) {
	this.svgNS = 'http://www.w3.org/2000/svg';
	this.xlinkNS = 'http://www.w3.org/1999/xlink';
	this.width = 0;
	this.height = 0;
	this.rotation = 0;
	var self = this;
	var componentElement;

	var init = function() {
		self.id = info.id;
		self.x = x;
		self.y = y;
		self.width = width;
		self.height = height;
		createDomStructure();
		self.componentElement = componentElement;
		window.eventsManager.addHandler(componentElement, 'click', clickHandler);
		controller.addListener('iconSelected', iconSelectedHandler);
		controller.addListener('roomSelected', roomSelectedHandler);
	};
	var createDomStructure = function() {
		componentElement = document.createElementNS(self.svgNS, 'svg:image');
		componentElement.setAttribute('x', x);
		componentElement.setAttribute('y', y);
		componentElement.setAttribute('width', width);
		componentElement.setAttribute('height', height);
		componentElement.setAttributeNS(self.xlinkNS, 'href', info.image);
	};
	var clickHandler = function(event) {
		if (window.floorMapLogics.getSelectorType() != 1 || window.floorMapLogics.getEditingMode()) {
			return;
		}
		window.floorMapLogics.selectObject(self);
		window.floorMapLogics.selectIcon(self.id);
	};
	var doWhenTrue = function(condition, after) {
		if (!condition()) {
			setTimeout(function() {
				doWhenTrue(condition, after);
			}, 250);
		}
		else {
			after();
		}
	};
	var iconSelectedHandler = function() {
		var selectedObject = floorMapLogics.getSelectedObject();
		if (selectedObject && selectedObject == self) {
			self.setOpacity(0.65);
		}
		else {
			self.setOpacity(1);
		}
	};
	var roomSelectedHandler = function() {
		self.setOpacity(1);
	};
	this.setWidth = function(newWidth) {
		self.width = newWidth;
		componentElement.setAttribute('width', newWidth);
	};
	this.setHeight = function(newHeight) {
		self.height = newHeight;
		componentElement.setAttribute('height', newHeight);
	}
	this.setLocation = function(x, y) {
		self.x = x;
		self.y = y;
		componentElement.setAttribute('x', x);
		componentElement.setAttribute('y', y);
	};
	this.setRotation = function(rotation) {
		self.rotation = rotation;
		if (rotation > 0) {
			var rotationX = parseFloat(self.x) + parseFloat(self.width) / 2;
			var rotationY = parseFloat(self.y) + parseFloat(self.height) / 2;
			componentElement.setAttribute('transform', 'rotate(' + rotation + ',' + rotationX + ',' + rotationY + ')');
		}
		else {
			componentElement.setAttribute('transform', '');
		}
	};
	this.setOpacity = function(opacity) {
		componentElement.style.opacity = opacity;
	}
	this.remove = function() {
		if (componentElement.parentNode) {
			componentElement.parentNode.removeChild(componentElement);
		}
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
window.FloorMapPreviewPolygon = function() {
	var componentElement;

	var init = function() {
		componentElement = document.createElementNS('http://www.w3.org/2000/svg', 'svg:polyline');
		componentElement.setAttribute('fill', 'none');
		componentElement.setAttribute('stroke', 'black');
		componentElement.setAttribute('stroke-width', '0.25%');
	};
	this.setPoints = function(points) {
		var pointsString = '';
		for (var i = 0; i < points.length; ++i) {
			var point = points[i];
			pointsString += point.x + ',' + point.y + ' ';
		}
		componentElement.setAttributeNS(null, 'points', pointsString);
	};
	this.getComponentElement = function() {
		return componentElement;
	};
	init();
};
window.sectionRegionComponent = function(roomData) {
	var self = this;

	this.svgNS = 'http://www.w3.org/2000/svg';
	this.xlinkNS = 'http://www.w3.org/1999/xlink';

	this.selected = false;

	this.componentElement = false;

	this.init = function() {
		self.roomData = roomData;
		self.id = roomData.id;
		self.title = roomData.title;
		if (self.createDomStructure()) {
			window.eventsManager.addHandler(self.componentElement, 'click', self.clickHandler);
			controller.addListener('roomSelected', self.roomSelectedHandler);
			controller.addListener('roomUpdated', self.roomUpdatedHandler);
			controller.addListener('objectSelected', iconSelectedHandler);

			self.recalculate();
			self.refreshStatus();
		}
	}
	this.createDomStructure = function() {
		self.componentElement = document.createElementNS(self.svgNS, 'svg:polygon');

		return true;
	}
	this.recalculate = function() {
		var pointsString = '';

		for (var i = 0; i < self.roomData.nodes.length; i++) {
			var nodeData = self.roomData.nodes[i];
			pointsString += nodeData.x + ',' + nodeData.y + ' ';
		}
		self.componentElement.setAttributeNS(null, 'points', pointsString);
	}
	this.clickHandler = function() {
		if (window.floorMapLogics.getSelectorType() != 0 || window.floorMapLogics.getEditingMode()) {
			return;
		}
		window.floorMapLogics.selectObject(self);
		window.floorMapLogics.selectRoom(self.roomData.id);
	}
	this.roomUpdatedHandler = function(newId) {
		if (newId == self.roomData.id) {
			self.recalculate();
			self.refreshStatus();
			if (self.roomData.nodes.length) {
				floorMapLogics.selectObject(self);
			}
		}
	}
	this.roomSelectedHandler = function(newId) {
		if (newId == self.roomData.id && !self.selected) {
			if (self.roomData.nodes.length) {
				floorMapLogics.selectObject(self);
			}
			self.setSelected();
		}
		else if (newId != self.roomData.id && self.selected) {
			self.setUnselected();
		}
	}
	var iconSelectedHandler = function() {
		self.setUnselected();
	};
	this.setSelected = function() {
		this.selected = true;
		this.refreshStatus();
	}
	this.setUnselected = function() {
		this.selected = false;
		this.refreshStatus();
	}
	this.refreshStatus = function() {
		if (self.componentElement) {
			self.componentElement.setAttribute('fill', 'red');
			self.componentElement.setAttribute('stroke', 'black');
			self.componentElement.setAttribute('stroke-width', '0.25%');

			if (self.selected) {
				self.componentElement.setAttribute('opacity', '1');
			}
			else {
				self.componentElement.setAttribute('opacity', '0.5');
			}
		}
	}
	this.init();
}
window.regionPointComponent = function(x, y) {
	this.init = function() {
		this.x = x;
		this.y = y;
		if (this.createDomStructure()) {

		}
	}
	this.createDomStructure = function() {
		var circleElement = document.createElementNS(self.svgNS, 'svg:circle')
		circleElement.setAttributeNS(null, 'cx', this.x);
		circleElement.setAttributeNS(null, 'cy', this.y);
		circleElement.setAttributeNS(null, 'r', '0.5%');
		circleElement.setAttributeNS(null, 'fill', '#0000ff');
		circleElement.setAttribute('stroke', '#000000');
		circleElement.setAttribute('stroke-width', '1');

		self.componentElement = circleElement;

		return true;
	}
	this.setColor = function(color) {
		self.componentElement.setAttributeNS(null, 'fill', color);
	};
	this.setLocation = function(x, y) {
		this.x = x;
		this.y = y;
		self.componentElement.setAttributeNS(null, 'cx', this.x);
		self.componentElement.setAttributeNS(null, 'cy', this.y);
	}

	this.svgNS = 'http://www.w3.org/2000/svg';

	this.x = false;
	this.y = false;

	var self = this;
	this.componentElement = false;
	this.init();
}