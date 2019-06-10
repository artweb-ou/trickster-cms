var ScalableComponent = function() {
    var scaledElement;
    var gestureElement;
    var beforeStartCallback;
    var afterStartCallback;
    var afterScaleCallback;
    var endCallback;
    var speedX = 1;
    var speedY = 1;
    var minWidth;
    var minHeight;
    var maxWidth;
    var maxHeight;
    var positionedElement;
    var stopPropagationStart;
    var stopPropagationMove;
    var stopPropagationEnd;

    var scale;
    var startWidth;
    var startHeight;
    var currentWidth;
    var currentHeight;

    var startF0x;
    var startF0y;
    var startF1x;
    var startF1y;
    var startDistance;

    var positionStartX;
    var positionStartY;

    var f0x;
    var f0y;
    var f1x;
    var f1y;

    var self = this;
    this.registerScalableElement = function(parameters) {
        if (typeof parameters == 'object') {
            if (parameters.scaledElement != undefined) {
                scaledElement = parameters.scaledElement;
            }
            if (parameters.gestureElement != undefined) {
                gestureElement = parameters.gestureElement;
            } else {
                gestureElement = scaledElement;
            }
            if (typeof parameters.beforeStartCallback == 'function') {
                beforeStartCallback = parameters.beforeStartCallback;
            }
            if (typeof parameters.afterStartCallback == 'function') {
                afterStartCallback = parameters.afterStartCallback;
            }
            if (typeof parameters.afterScaleCallback == 'function') {
                afterScaleCallback = parameters.afterScaleCallback;
            }
            if (typeof parameters.endCallback == 'function') {
                endCallback = parameters.endCallback;
            }
            if (typeof parameters.speedX != 'undefined') {
                speedX = parseFloat(parameters.speedX, 10);
            } else {
                speedX = 1;
            }
            if (typeof parameters.speedY != 'undefined') {
                speedY = parseFloat(parameters.speedY, 10);
            } else {
                speedY = 1;
            }
            if (typeof parameters.minWidth != 'undefined') {
                minWidth = parseInt(parameters.minWidth, 10);
            }
            if (typeof parameters.minHeight != 'undefined') {
                minHeight = parseInt(parameters.minHeight, 10);
            }
            if (typeof parameters.maxWidth != 'undefined') {
                maxWidth = parseInt(parameters.maxWidth, 10);
            }
            if (typeof parameters.maxHeight != 'undefined') {
                maxHeight = parseInt(parameters.maxHeight, 10);
            }
            if (typeof parameters.positionedElement != 'undefined') {
                positionedElement = parameters.positionedElement;
            }
            if (typeof parameters.stopPropagationStart != 'undefined') {
                stopPropagationStart = parameters.stopPropagationStart;
            }
            if (typeof parameters.stopPropagationMove != 'undefined') {
                stopPropagationMove = parameters.stopPropagationMove;
            }
            if (typeof parameters.stopPropagationEnd != 'undefined') {
                stopPropagationEnd = parameters.stopPropagationEnd;
            }
            initScalableElement();
        }
    };
    this.unRegisterScalableElement = function() {
        removeScalableElement();
    };
    this.setScale = function(scale) {
        initStartValues();
        applyScale(scale);
    };
    var initScalableElement = function() {
        removeScalableElement();
        eventsManager.addHandler(gestureElement, 'touchstart', startHandler, true);
        eventsManager.addHandler(gestureElement, 'pointerdown', startHandler, true);
    };
    var initStartValues = function() {
        startWidth = scaledElement.offsetWidth;
        startHeight = scaledElement.offsetHeight;
        if (positionedElement) {
            positionStartX = positionedElement.offsetLeft;
            positionStartY = positionedElement.offsetTop;
        }
    };
    var removeScalableElement = function() {
        eventsManager.removeHandler(gestureElement, 'touchstart', startHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchmove', moveHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchcancel', cancelHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchend', endHandler, true);
        if (window.PointerEvent) {
            gestureElement.removeEventListener('pointerdown', startHandler);
            gestureElement.removeEventListener('pointerup', endHandler);
            gestureElement.removeEventListener('pointerleave', cancelHandler);
            gestureElement.removeEventListener('pointermove', moveHandler);
        }
    };
    var startHandler = function(eventInfo) {
        if (stopPropagationStart) {
            eventsManager.cancelBubbling(eventInfo);
        }
        eventsManager.preventDefaultAction(eventInfo);
        if (eventInfo.touches != undefined && eventInfo.touches.length > 1) {
            scale = 1;

            startF0x = eventInfo.touches[0].pageX;
            startF0y = eventInfo.touches[0].pageY;
            startF1x = eventInfo.touches[1].pageX;
            startF1y = eventInfo.touches[1].pageY;

            startDistance = Math.pow(Math.pow(startF1x - startF0x, 2) + Math.pow(startF1y - startF0y, 2), 0.5);

            initStartValues();

            if ((beforeStartCallback == undefined) || beforeStartCallback(compileInfo(eventInfo))) {
                eventsManager.addHandler(gestureElement, 'touchmove', moveHandler, true);
                eventsManager.addHandler(gestureElement, 'touchend', endHandler, true);
                eventsManager.addHandler(gestureElement, 'touchcancel', cancelHandler, true);
                if (window.PointerEvent) {
                    gestureElement.addEventListener('pointerup', endHandler);
                    gestureElement.addEventListener('pointerleave', cancelHandler);
                    gestureElement.addEventListener('pointermove', moveHandler);
                }
                if (afterStartCallback) {
                    afterStartCallback(compileInfo(eventInfo));
                }
            }
        }
    };
    var moveHandler = function(eventInfo) {
        if (stopPropagationMove) {
            eventsManager.cancelBubbling(eventInfo);
        }
        eventsManager.preventDefaultAction(eventInfo);
        if (eventInfo.touches != undefined && eventInfo.touches.length > 1) {
            f0x = eventInfo.touches[0].pageX;
            f0y = eventInfo.touches[0].pageY;
            f1x = eventInfo.touches[1].pageX;
            f1y = eventInfo.touches[1].pageY;
            var distance = Math.pow(Math.pow(f1x - f0x, 2) + Math.pow(f1y - f0y, 2), 0.5);
            scale = distance / startDistance;

            applyScale(scale);
        }
    };
    var endHandler = function(eventInfo) {
        if (stopPropagationEnd) {
            eventsManager.cancelBubbling(eventInfo);
        }
        eventsManager.preventDefaultAction(eventInfo);
        eventsManager.removeHandler(gestureElement, 'touchmove', moveHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchend', endHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchcancel', cancelHandler, true);
        if (window.PointerEvent) {
            gestureElement.addEventListener('pointerup', endHandler); // Releasing the pointer
            gestureElement.addEventListener('pointerleave', cancelHandler); // Pointer gets out of the SVG area
            gestureElement.addEventListener('pointermove', moveHandler); // Pointer is moving
        }

        if (endCallback) {
            endCallback(compileInfo(eventInfo));
        }
    };
    var cancelHandler = function(eventInfo) {
        endHandler(eventInfo);
    };
    var applyScale = function(scale) {
        var change = 1 - scale;

        currentWidth = startWidth - startWidth * change * speedX;

        if (currentWidth > maxWidth) {
            currentWidth = maxWidth;
        }
        if (currentWidth < minWidth) {
            currentWidth = minWidth;
        }

        currentHeight = startHeight - startHeight * change * speedY;
        if (currentHeight > maxHeight) {
            currentHeight = maxHeight;
        }
        if (currentHeight < minHeight) {
            currentHeight = minHeight;
        }

        scaledElement.style.width = currentWidth + 'px';
        scaledElement.style.height = currentHeight + 'px';
        if (positionedElement) {
            var x = positionStartX + (startWidth - currentWidth) / 2;
            var y = positionStartY + (startHeight - currentHeight) / 2;
            positionedElement.style.left = x + 'px';
            positionedElement.style.top = y + 'px';
        }

        if (afterScaleCallback) {
            afterScaleCallback(compileInfo());
        }
    };
    var compileInfo = function(eventInfo) {
        return {
            'event': eventInfo,
            'speedX': speedX,
            'speedY': speedY,
            'minWidth': minWidth,
            'minHeight': minHeight,
            'maxWidth': maxWidth,
            'maxHeight': maxHeight,
            'scale': scale,
            'startWidth': startWidth,
            'startHeight': startHeight,
            'currentWidth': currentWidth,
            'currentHeight': currentHeight,

            'startF0x': startF0x,
            'startF0y': startF0y,
            'startF1x': startF1x,
            'startF1y': startF1y,
            'startDistance': startDistance,

            'f0x': f0x,
            'f0y': f0y,
            'f1x': f1x,
            'f1y': f1y,
        };
    };
};