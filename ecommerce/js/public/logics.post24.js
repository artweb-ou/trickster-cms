window.post24Logics = new function() {
	var self = this;
	var automatesList;
	var regionsList;
	var regionsIndex;
	var currentRegionId;

	var importData = function() {
		automatesList = [];
		regionsList = [];
		regionsIndex = {};
		if (window.post24List != undefined) {
			var info = window.post24List;
			for (var i = 0, length = info.length; i < length; i++) {
				var automate = new Post24Automate(info[i]);
				automatesList.push(automate);
				if (info[i] != undefined) {
					var regionId = info[i].A1_NAME;
					if (regionsIndex[regionId] == undefined) {
						var region = new Post24Region(info[i]);
						regionsList.push(region);
						regionsIndex[regionId] = region;
					}
					regionsIndex[regionId].registerAutomate(automate);
				}
				if (!currentRegionId) {
					currentRegionId = regionId;
				}
			}
			regionsList.sort(function(a, b) {
				if (a.getName() < b.getName()) {
					return -1;
				}
				if (a.getName() > b.getName()) {
					return 1;
				}
				return 0;
			});
		}
	};

	this.getRegionsList = function() {
		if (regionsList == null) {
			importData();
		}
		return regionsList;
	};
	this.getCountryRegionsList = function(countryCode) {
		var countryRegions = [];
		if (regionsList == null) {
			importData();
		}
		for (var i = 0; i < regionsList.length; i++) {
			if (regionsList[i].getCountry().toLowerCase() == countryCode.toLowerCase()) {
				countryRegions.push(regionsList[i]);
			}
		}
		return countryRegions;
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
			currentRegionId = regionsIndex[regionId].getId();
		}
		controller.fireEvent('post24RegionSelected', currentRegionId);
	};
	this.getCurrentRegion = function() {
		var currentRegion = false;
		if (regionsIndex[currentRegionId] != undefined) {
			currentRegion = regionsIndex[currentRegionId];
		}
		return currentRegion;
	};
};
window.Post24Region = function(info) {
	var self = this;
	var id;
	var name;
	var automatesList;
	var country;
	var init = function() {
		automatesList = [];
		id = info.A1_NAME;
		name = info.A1_NAME;
		country = info.A0_NAME;
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
	this.getCountry = function() {
		return country;
	};
	init();
};
window.Post24Automate = function(info) {
	var self = this;
	var name;
	var city;
	var street;
	var house;
	var init = function() {

		name = info.NAME;

		if (info.A2_NAME != undefined) {
			city = info.A2_NAME;
		}
		if (info.A5_NAME != undefined) {
			street = info.A5_NAME;
		}
		if (info.A7_NAME != undefined) {
			house = info.A7_NAME;
		}
	};
	this.getName = function() {
		return name;
	};
	this.getFullTitle = function() {
		var title = name;
		if (city && city !== 'NULL') {
			title += ', ' + city;
		}
		if (street && street !== 'NULL') {
			title += ', ' + street;
			if (house && house !== 'NULL') {
				title += ', ' + house;
			}
		}
		return title;
	};

	init();
};