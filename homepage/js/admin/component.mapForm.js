window.MapFormComponent = function(componentElement) {
	var coordinatesInputElement, geocodingButton, geocodingResultElement;
	var detailsInputElements = [];

	var init = function() {
		createDomStructure();
		eventsManager.addHandler(geocodingButton, "click", onGeocodeButtonClick);
	};

	var createDomStructure = function() {
		detailsInputElements = [
			_('.map_country', componentElement)[0], _('.map_region', componentElement)[0], _('.map_city', componentElement)[0], _('.map_address', componentElement)[0], _('.map_zip', componentElement)[0]
		];
		geocodingButton = _('.map_geocoding_button', componentElement)[0];
		coordinatesInputElement = _('.map_coordinates_input', componentElement)[0];
		geocodingResultElement = _('.map_geocoding_result', componentElement)[0];
	};

	var onGeocodeButtonClick = function(event) {
		eventsManager.preventDefaultAction(event);
		var phrase = generateSearchPhrase();
		if (phrase !== undefined) {
			var response = sendRequest(phrase);
			if (response && response.status == "OK") {
				var resultData = response.results[0];
				if (resultData) {
					geocodingResultElement.innerHTML = resultData["formatted_address"];
					var locationData = resultData.geometry.location;
					coordinatesInputElement.value = locationData.lat + "," + locationData.lng;
				}
			} else {
				geocodingResultElement.innerHTML = window.translationsLogics.get("map.coordinates_not_found");
			}
		}
	};

	var generateSearchPhrase = function() {
		var result = "";
		var flag = true;
		for (var i = 0; i != detailsInputElements.length; i++) {
			if (detailsInputElements[i] !== undefined && detailsInputElements[i].value) {
				result += " " + detailsInputElements[i].value;
				flag = false;
			}
		}
		if(flag) {
			result = coordinatesInputElement.value;
		}
		return result;
	};

	var sendRequest = function(address) {
		var result = false;
		var url = 'https://maps.google.com/maps/api/geocode/json?address=' + encodeURI(address) + '&sensor=false&key=AIzaSyD6IK7At5KLa_vYFFxGcE6ml9VC2WQTWHw';
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", url, false);
		xmlHttp.send(null);
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200 && xmlHttp.responseText != "Not found") {
			result = eval("(" + xmlHttp.responseText + ")");
		}
		return result;
	};
	init();
};