var DraggableComponent = function() {
    var draggableElement;
    var parentElement;
    var gestureElement;
    var boundariesElement;

    var boundariesPadding;

    var startElementX;
    var startElementY;
    var currentElementX;
    var currentElementY;

    var startTouchX;
    var startTouchY;
    var currentTouchX;
    var currentTouchY;

    var beforeStartCallback;
    var startCallback;
    var beforeMoveCallback;
    var afterMoveCallback;
    var endCallback;
    var stopPropagationStart;
    var stopPropagationMove;
    var stopPropagationEnd;

    var self = this;
    this.registerDraggableElement = function(parameters) {
        if (typeof parameters == 'object') {
            if (parameters.draggableElement != undefined) {
                draggableElement = parameters.draggableElement;
            }

            if (parameters.parentElement != undefined) {
                parentElement = parameters.parentElement;
            } else if (draggableElement) {
                parentElement = draggableElement.parentNode;
            }

            if (parameters.gestureElement != undefined) {
                gestureElement = parameters.gestureElement;
            } else if (draggableElement) {
                gestureElement = draggableElement;
            }

            if (parameters.boundariesElement != undefined) {
                boundariesElement = parameters.boundariesElement;
            }
            if (parameters.boundariesPadding != undefined) {
                boundariesPadding = parseFloat(parameters.boundariesPadding);
            } else {
                boundariesPadding = 0;
            }

            if (typeof parameters.beforeStartCallback == 'function') {
                beforeStartCallback = parameters.beforeStartCallback;
            }
            if (typeof parameters.startCallback == 'function') {
                startCallback = parameters.startCallback;
            }
            if (typeof parameters.beforeMoveCallback == 'function') {
                beforeMoveCallback = parameters.beforeMoveCallback;
            }
            if (typeof parameters.afterMoveCallback == 'function') {
                afterMoveCallback = parameters.afterMoveCallback;
            }
            if (typeof parameters.endCallback == 'function') {
                endCallback = parameters.endCallback;
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

            initDraggableElement();
        }
    };
    this.unRegisterDraggableElement = function() {
        removeDraggableElement();
    };
    var initDraggableElement = function() {
        removeDraggableElement();
        eventsManager.addHandler(gestureElement, 'touchstart', startHandler, true);
        gestureElement.removeEventListener('pointerdown', startHandler);
    };
    var removeDraggableElement = function() {
        eventsManager.removeHandler(gestureElement, 'touchstart', startHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchmove', moveHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchcancel', endHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchend', endHandler, true);
        if (window.PointerEvent) {
            gestureElement.removeEventListener('pointerdown', startHandler);
            gestureElement.removeEventListener('pointerup', endHandler);
            gestureElement.removeEventListener('pointerleave', endHandler);
            gestureElement.removeEventListener('pointermove', moveHandler);
        }
    };
    var startHandler = function(eventInfo) {
        if (stopPropagationStart) {
            eventsManager.cancelBubbling(eventInfo);
        }

        eventsManager.preventDefaultAction(eventInfo);
        if (eventInfo.touches != undefined && eventInfo.touches.length == 1) {
            startElementX = draggableElement.offsetLeft;
            startElementY = draggableElement.offsetTop;
            startTouchX = eventInfo.touches[0].pageX;
            startTouchY = eventInfo.touches[0].pageY;

            if ((beforeStartCallback == undefined) || beforeStartCallback(compileInfo(eventInfo))) {
                eventsManager.addHandler(gestureElement, 'touchmove', moveHandler, true);
                eventsManager.addHandler(gestureElement, 'touchend', endHandler, true);
                eventsManager.addHandler(gestureElement, 'touchcancel', endHandler, true);
                if (window.PointerEvent) {
                    gestureElement.addEventListener('pointerup', endHandler);
                    gestureElement.addEventListener('pointerleave', endHandler);
                    gestureElement.addEventListener('pointermove', moveHandler);
                }
                if (startCallback) {
                    startCallback(compileInfo(eventInfo));
                }
            }
        }
    };
    var moveHandler = function(eventInfo) {
        if (stopPropagationMove) {
            eventsManager.cancelBubbling(eventInfo);
        }
        eventsManager.preventDefaultAction(eventInfo);
        if (eventInfo.touches != undefined && eventInfo.touches.length == 1) {
            currentTouchX = eventInfo.touches[0].pageX;
            currentTouchY = eventInfo.touches[0].pageY;

            currentElementX = startElementX + currentTouchX - startTouchX;
            currentElementY = startElementY + currentTouchY - startTouchY;
            if (boundariesElement) {
                var minX;
                var maxX;
                var minY;
                var maxY;

                if (currentElementX > (minX = boundariesElement.offsetWidth * boundariesPadding)) {
                    currentElementX = minX;
                } else if (currentElementX < (maxX = (boundariesElement.offsetWidth * (1 - boundariesPadding) - draggableElement.offsetWidth))) {
                    currentElementX = maxX;
                }

                if (currentElementY > (minY = boundariesElement.offsetHeight * boundariesPadding)) {

                    currentElementY = minY;
                }
                if (currentElementY < (maxY = boundariesElement.offsetHeight * (1 - boundariesPadding) - draggableElement.offsetHeight)) {
                    currentElementY = maxY;
                }
            }
            if ((beforeMoveCallback == undefined) || beforeMoveCallback(compileInfo(eventInfo))) {
                draggableElement.style.left = currentElementX + 'px';
                draggableElement.style.top = currentElementY + 'px';

                if (afterMoveCallback) {
                    afterMoveCallback(compileInfo(eventInfo));
                }
            }
        }

    };
    var endHandler = function(eventInfo) {
        if (stopPropagationEnd) {
            eventsManager.cancelBubbling(eventInfo);
        }
        eventsManager.preventDefaultAction(eventInfo);
        eventsManager.removeHandler(gestureElement, 'touchmove', moveHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchend', endHandler, true);
        eventsManager.removeHandler(gestureElement, 'touchcancel', endHandler, true);
        if (window.PointerEvent) {
            gestureElement.addEventListener('pointerup', endHandler);
            gestureElement.addEventListener('pointerleave', endHandler);
            gestureElement.addEventListener('pointermove', moveHandler);
        }
        if (endCallback) {
            endCallback(compileInfo(eventInfo));
        }
    };

    var compileInfo = function(eventInfo) {
        return {
            'draggableElement': draggableElement,
            'parentElement': parentElement,
            'gestureElement': gestureElement,
            'event': eventInfo,

            'startElementX': startElementX,
            'startElementY': startElementY,
            'currentElementX': currentElementX,
            'currentElementY': currentElementY,

            'startTouchX': startTouchX,
            'startTouchY': startTouchY,
            'currentTouchX': currentTouchX,
            'currentTouchY': currentTouchY,
        };
    };
};