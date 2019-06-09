window.dpdLogics = new function() {
    var self = this;
    var pointsList;
    var regionsList;
    var regionsIndex;
    var currentRegionId;

    var importData = function() {
        pointsList = [];
        regionsList = [];
        regionsIndex = {};
        if (typeof window.dpdList !== 'undefined') {
            var info = window.dpdList;
            for (var i = 0, length = info.length; i < length; i++) {
                var point = new DpdPoint(info[i]);
                pointsList.push(point);

                var region = new DpdRegion(info[i]);
                var regionId = region.getId();
                if (regionsIndex[regionId] == undefined) {
                    regionsList.push(region);
                    regionsIndex[regionId] = region;
                }
                regionsIndex[regionId].registerPoint(point);

                if (!currentRegionId) {
                    currentRegionId = regionId;
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
        controller.fireEvent('dpdRegionSelected', currentRegionId);
    };
    this.getCurrentRegion = function() {
        var currentRegion = false;
        if (regionsIndex[currentRegionId] != undefined) {
            currentRegion = regionsIndex[currentRegionId];
        }
        return currentRegion;
    };
};
window.DpdRegion = function(info) {
    var self = this;
    var id;
    var name;
    var country;
    var pointsList;
    var init = function() {
        pointsList = [];
        name = info.Sh_city;
        country = info.Sh_country;
        id = country + '_' + name;
    };
    this.registerPoint = function(info) {
        pointsList.push(info);
    };
    this.getPointsList = function() {
        return pointsList;
    };
    this.getName = function() {
        return name;
    };
    this.getCountry = function() {
        return country;
    };
    this.getId = function() {
        return id;
    };
    init();
};
window.DpdPoint = function(info) {
    var self = this;
    var name;
    var id;
    var city;
    var country;
    var street;
    var postCode;
    var init = function() {
        name = info.Pudo_name;
        if (info.Sh_country != undefined) {
            country = info.Sh_country;
        }
        if (info.Sh_city != undefined) {
            city = info.Sh_city;
        }
        if (info.Sh_street != undefined) {
            street = info.Sh_street;
        }
        if (info.Sh_postal != undefined) {
            postCode = info.Sh_postal;
        }
        if (info.Sh_pudo_id != undefined) {
            id = info.Sh_pudo_id;
        }
    };
    this.getName = function() {
        return name;
    };
    this.getFullTitle = function() {
        var title = name;
        if (city) {
            title += ', ' + city;
        }
        if (street) {
            title += ', ' + street;
            if (postCode) {
                title += ', ' + postCode;
            }
        }
        return title;
    };

    init();
};