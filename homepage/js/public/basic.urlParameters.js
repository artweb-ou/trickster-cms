window.urlParameters = new function() {
	var self = this;
	var currentParameters = [];
	var initialized = false;

	var init = function() {
		eventsManager.addHandler(window, 'popstate', stateChangeHandler);
		parseLocationParameters();
		initialized = true;
	};
	var stateChangeHandler = function() {
		parseLocationParameters();
		controller.fireEvent('urlParametersUpdate', currentParameters);
	};
	var parseLocationParameters = function() {
		var path = '';
		if (initialized) {
			var returnLocation = history.location || document.location;
			path = returnLocation.pathname;
		} else {
			// we don't care where the user's been before initialization
			path = document.location.pathname;
			path += getHashBangPart();
		}
		currentParameters = {};

		var parts = path.split('/');
		for (var i = 0; i < parts.length; i++) {
			var strings = parts[i].split(":");
			if (strings.length == 2) {
				currentParameters[strings[0]] = strings[1];
			}
		}
	};

	var parseBaseUrl = function() {
		var url = location.protocol + '//' + location.host + location.pathname;
		var protocolColonPosition = url.indexOf('://');
		for (; ;) {
			var colonPosition = url.lastIndexOf(':');
			if (colonPosition <= protocolColonPosition) {
				break;
			}
			url = url.slice(0, url.slice(0, colonPosition).lastIndexOf('/') + 1);
		}
		url += getHashBangPartWithoutParameters();
		return url;
	};

	var getHashBangPart = function() {
		var fragment = document.location.hash;
		if (fragment.indexOf('#!') == 0) {
			return fragment.slice(2);
		}
		return '';
	};

	var getHashBangPartWithoutParameters = function() {
		var hashbang = getHashBangPart();
		if (hashbang) {
			for (; ;) {
				var colonPosition = hashbang.lastIndexOf(':');
				if (colonPosition < 0) {
					break;
				}
				hashbang = hashbang.slice(0, hashbang.slice(0, colonPosition).lastIndexOf('/') + 1);
			}
		}
		return hashbang;
	};

	var updateHistoryState = function() {
		var url = parseBaseUrl();
		for (var i in currentParameters) {
			url += i + ":" + currentParameters[i] + "/" + location.search;
		}
		history.pushState(null, null, url);
	};

	var updateParameter = function(name, value) {
		if (value != false) {
			currentParameters[name] = value;
		} else if (typeof currentParameters[name] !== 'undefined') {
			delete currentParameters[name];
		}
	};

	// TODO: do something with the "ninja" workaround
	this.setParameter = function(name, value, ninjaUpdate) {
		if (value == false) {
			delete currentParameters[name];
		} else {
			currentParameters[name] = value;
		}
		if (!ninjaUpdate) {
			updateHistoryState();
			controller.fireEvent('urlParametersUpdate', currentParameters);
		}
	};

	this.updateParameters = function(newParameters) {
		for (var id in newParameters) {
			updateParameter(id, newParameters[id]);
		}
		updateHistoryState();
		controller.fireEvent('urlParametersUpdate', currentParameters);
	};

	this.setParameters = function(newParameters) {
		currentParameters = newParameters;
		updateHistoryState();
		controller.fireEvent('urlParametersUpdate', currentParameters);
	};

	this.setUrl = function(newUrl) {
		history.pushState(null, null, newUrl);
		parseLocationParameters();
		controller.fireEvent('urlParametersUpdate', currentParameters);
	};

	this.getParameter = function(name) {
		if (typeof currentParameters[name] !== 'undefined') {
			return currentParameters[name];
		}
		return false;
	};

	this.getParameters = function() {
		return currentParameters;
	};

	controller.addListener("initLogics", init);
};