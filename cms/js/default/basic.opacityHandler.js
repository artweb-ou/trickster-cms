window.opacityHandler = new function() {
	var detectOpacityType = function() {
		if (typeof(document.body.style.opacity) == 'string') {
			opacityType = 'opacity';
		} else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1] >= 5.5) {
			//			var version = navigator.appVersion.match(/MSIE ([\d.]+);/)[1];
			//			if (version >= 8 && version < 9)
			//			{
			//				opacityRecursive = true;
			//			}
			opacityType = 'filter';
		}
	}
	this.setOpacity = function(element, opacity) {
		if (element) {
			if (!opacityType) {
				detectOpacityType();
			}
			if (opacity < 0) {
				opacity = 0;
			}
			if (opacityType == "filter") {
				if (element.currentStyle) {
					if (element.currentStyle.filter != '' && element.style.filter == '') {
						element.style.filter += element.currentStyle.filter;
					}
				}

				try {
					element.filters.item('DXImageTransform.Microsoft.alpha').opacity = Math.round(opacity * 100);
				} catch(error) {
					element.style.filter += "progid:DXImageTransform.Microsoft.Alpha(style=0, opacity=" + Math.round(opacity * 100) + ", FinishOpacity=" + Math.round(opacity * 100) + ")";
				}
			} else {
				element.style[opacityType] = opacity.toFixed(2);
			}
		}

		if (opacityRecursive) {
			for (var i = 0; i < element.childNodes.length; i++) {
				if (element.childNodes[i].nodeType == '1') {
					var position = element.childNodes[i].currentStyle['position'];
					if (position == 'absolute' || position == 'relative') {
						self.setOpacity(element.childNodes[i], opacity);
					}
				}
			}
		}
	}
	this.getOpacity = function(element) {
		if (!opacityType) {
			detectOpacityType();
		}
		if (opacityType == "filter") {
			if (element.filters) {
				try {
					var opacity = element.filters.item("DXImageTransform.Microsoft.Alpha").opacity / 100;
				} catch(error) {
					var opacity = 1;
				}
			}
		} else if (window.getComputedStyle) {
			var opacity = document.defaultView.getComputedStyle(element, null).getPropertyValue(opacityType);
		}
		return parseFloat(opacity);
	}
	var self = this;
	var opacityType = false;
	var opacityRecursive = false;
}