window.anchorParameters = new function() {
	this.initHandler = function() {
		self.detectBaseURL();
	};
	this.startHandler = function() {
		window.setInterval(self.checkParametersChange, self.checkInterval);
	};
	this.detectBaseURL = function() {
		var hrefString = document.location.href;
		if (hrefString.search('#') != '-1') {
			var hashStrings = hrefString.split('#');
			hrefString = hashStrings[0];
		}
		self.baseURL = hrefString;
	};
	this.checkParametersChange = function() {
		if (self.olderHref != document.location.href) {
			self.olderHref = document.location.href;
			self.handleHrefChange();
		}
	};
	this.handleHrefChange = function() {
		var hrefString = document.location.href;
		var parametersString = '';
		if (hrefString.search('#') != '-1') {
			var hashStrings = hrefString.split('#');
			parametersString = hashStrings[hashStrings.length - 1];
		}

		self.parseHashParameters(parametersString);
	};
	this.parseHashParameters = function(parametersString) {
		parameters = {};
		var pairsList = parametersString.split(this.pairsDelimiter);
		for (var i = 0; i < pairsList.length; i++) {
			var words = pairsList[i].split(this.valueDelimiter);
			if (words.length == 2) {
				var name = words[0];
				var value = false;
				if (words[1]) {
					value = decodeURIComponent(words[1]);
				}
				parameters[name] = value;
			}
		}
		controller.fireEvent('anchorParametersUpdate', parameters);
	};
	this.getParameter = function(name) {
		var result = false;
		if (parameters[name]) {
			result = parameters[name];
		}
		return result;
	};
	var self = this;
	var parameters = {};

	this.baseURL = false;
	this.olderHref = '';
	this.pairsDelimiter = '&';
	this.valueDelimiter = '=';
	this.checkInterval = 200;
	controller.addListener("initLogics", this.initHandler);
	controller.addListener("startApplication", this.startHandler);
};