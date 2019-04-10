window.smartPostLogics = new function() {
	var self = this;
	var automatesList;
	var regionsList;
	var regionsIndex;
	var currentRegionId;

	var importData = function() {
		automatesList = [];
		regionsList = [];
		regionsIndex = {};

		if (typeof window.places_info != 'undefined') {
			var info = window.places_info;

			for (var i = 0, length = info.length; i < length; i++) {
				if (info[i].active == 0) {
					continue;
				}
				var automate = new SmartPostAutomate(info[i]);
				automatesList.push(automate);

				if (typeof info[i].group_name != 'undefined') {
					var regionId = info[i].group_name;
					if (typeof regionsIndex[regionId] == 'undefined') {
						var region = new SmartPostRegion(info[i]);
						regionsList.push(region);
						regionsIndex[regionId] = region;
					}
					regionsIndex[regionId].registerAutomate(automate);
				}
				regionsList.sort(function(a, b) {
					if (a.getGroupSort() < b.getGroupSort()) {
						return 1;
					}
					if (a.getGroupSort() > b.getGroupSort()) {
						return -1;
					}
					return 0;
				});
				if (!currentRegionId && regionsList.length > 0) {
					currentRegionId = regionsList[0].getName();
				}
			}
		}
	};

	this.getRegionsList = function() {
		if (regionsList == null) {
			importData();
		}
		return regionsList;
	};
	this.getRegion = function(regionId) {
		if (regionsList == null) {
			importData();
		}
		var result = false;
		if (regionsIndex[regionId] != undefined) {
			result = regionsIndex[regionId];
		}
		return result;
	};
	this.setCurrentRegion = function(regionId) {
		if (regionsList == null) {
			importData();
		}
		if (regionsIndex[regionId] != undefined) {
			currentRegionId = regionsIndex[regionId].getName();
		}
		controller.fireEvent('smartPostRegionSelected', currentRegionId);
	};
	this.getCurrentRegion = function() {
		var currentRegion = false;
		if (regionsIndex[currentRegionId] != undefined) {
			currentRegion = regionsIndex[currentRegionId];
		}
		return currentRegion;
	};
};
window.SmartPostRegion = function(info) {
	var id;
	var name;
	var groupSort;
	var automatesList;

	var init = function() {
		automatesList = [];
		id = info.group_id;
		name = info.group_name;
		groupSort = info.group_sort;
	};
	this.registerAutomate = function(info) {
		automatesList.push(info);
	};
	this.getAutomatesList = function() {
		return automatesList;
	};
	this.getName = function() {
		return name;
	};
	this.getId = function() {
		return id;
	};
	this.getGroupSort = function() {
		return groupSort;
	};
	init();
};
window.SmartPostAutomate = function(info) {
	var id;
	var name;
	var city;
	var address;

	var init = function() {
		name = info.name;
		id = info.id;

		if (typeof info.city != 'undefined') {
			address = info.city;
		}
		if (typeof info.address != 'undefined') {
			address = info.address;
		}
	};
	this.getId = function() {
		return id;
	};
	this.getName = function() {
		return name;
	};
	this.getFullTitle = function() {
		var title = name;
		if (city) {
			title += ', ' + city;
		}
		if (address) {
			title += ', ' + address;
		}
		return title;
	};

	init();
};