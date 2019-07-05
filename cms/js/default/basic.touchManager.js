window.touchManager = new function() {
    var self = this;
    var handlers = {};
    var eventsSet;
    var startEventName;
    var moveEventName;
    var endEventName;
    var cancelEventName;
    var pointerCache = {};

    var init = function() {
        handlers['start'] = [];
        handlers['end'] = [];
        handlers['move'] = [];
        handlers['cancel'] = [];
        eventsSet = self.getEventsSet();
        if (eventsSet == 'mouse') {
            captureStartEvent = captureStartEvent_mouse;
            captureEndEvent = captureEndEvent_mouse;
            compileEventInfo = compileEventInfo_mouse;
            startEventName = 'mousedown';
            moveEventName = 'mousemove';
            endEventName = 'mouseup';
            cancelEventName = 'mouseleave';
        } else if (eventsSet == 'touch') {
            compileEventInfo = compileEventInfo_touch;
            startEventName = 'touchstart';
            moveEventName = 'touchmove';
            endEventName = 'touchend';
            cancelEventName = 'touchcancel';
        } else if (eventsSet == 'pointer') {
            compileEventInfo = compileEventInfo_pointer;
            startEventName = 'pointerdown';
            moveEventName = 'pointermove';
            endEventName = 'pointerup';
            cancelEventName = 'pointerleave';
        } else if (eventsSet == 'mspointer') {
            compileEventInfo = compileEventInfo_mouse;
            startEventName = 'mspointerdown';
            moveEventName = 'mspointermove';
            endEventName = 'mspointerup';
            cancelEventName = 'mspointercancel';
        }
        controller.addListener('initDom', initDom);
    };
    var initDom = function() {
        switch (eventsSet) {
            case 'pointer':
            case 'mspointer':
                // cache pointers in these events for multi touch support
                document.body.addEventListener(endEventName, pointerUp, true);
                document.body.addEventListener(cancelEventName, pointerUp, true);
                document.body.addEventListener(startEventName, pointerDown, true);
                document.body.addEventListener(moveEventName, pointerMove, true);
                break;
        }
    };
    this.getEventsSet = function() {
        eventsSet = false;
        if (window.PointerEvent) {
            //IE >=11, somebody else?
            eventsSet = 'pointer';
        } else if (window.navigator.msPointerEnabled) {
            //IE mobile <=10
            eventsSet = 'mspointer';
        } else if ('ontouchstart' in window) {
            eventsSet = 'touch';
        } else if ('onmousedown' in window) {
            eventsSet = 'mouse';
        }
        self.getEventsSet = getEventsSet_return;
        return eventsSet;
    };
    var getEventsSet_return = function() {
        return eventsSet;
    };
    var captureStartEvent = function(event) {
        fireCallback('start', event);
    };
    var captureStartEvent_mouse = function(event) {
        if (event.button == 0) {
            fireCallback('start', event);
        }
    };
    var captureMoveEvent = function(event) {
        fireCallback('move', event);
    };
    var captureEndEvent = function(event) {
        fireCallback('end', event);
    };
    var captureCancelEvent = function(event) {
        fireCallback('cancel', event);
    };
    var captureEndEvent_mouse = function(event) {
        if (event.button == 0) {
            var eventType = 'end';
            fireCallback(eventType, event);
        }
    };
    var fireCallback = function(eventType, event) {
        var eventInfo = compileEventInfo(event);
        for (var i = 0; i < handlers[eventType].length; i++) {
            if (handlers[eventType][i]['element'] == eventInfo['currentTarget']) {
                handlers[eventType][i]['callback'](event, eventInfo);
            }
        }
    };
    var compileEventInfo;
    var compileEventInfo_touch = function(event) {
        var eventInfo = {
            'target': event.target,
            'currentTarget': event.currentTarget,
            'touches': event.touches,
        };
        if (typeof event.touches[0] !== 'undefined') {
            var firstTouch = event.touches[0];
            eventInfo['clientX'] = firstTouch.clientX;
            eventInfo['clientY'] = firstTouch.clientY;
            eventInfo['pageX'] = firstTouch.pageX;
            eventInfo['pageY'] = firstTouch.pageY;
        }
        return eventInfo;
    };
    var compileEventInfo_pointer = function(event) {
        var touches = [];
        for (var id in pointerCache) {
            touches.push(pointerCache[id]);
        }
        return {
            'touches': touches,
            'target': event.target,
            'currentTarget': event.currentTarget,
            'clientX': event.clientX,
            'clientY': event.clientY,
            'pageX': event.pageX,
            'pageY': event.pageY,
        };
    };
    var compileEventInfo_mouse = function(event) {
        var currentTouchInfo = {
            'clientX': event.clientX,
            'clientY': event.clientY,
            'pageX': event.pageX,
            'pageY': event.pageY,
        };
        return {
            'touches': [currentTouchInfo],
            'target': event.target,
            'currentTarget': event.currentTarget,
            'clientX': event.clientX,
            'clientY': event.clientY,
            'pageX': event.pageX,
            'pageY': event.pageY,
        };
    };
    var cachePointerEvent = function(event) {
        if (typeof event.pointerId !== undefined) {
            pointerCache[event.pointerId] = {
                'clientX': event.clientX,
                'clientY': event.clientY,
                'pageX': event.pageX,
                'pageY': event.pageY,
            };
        }
    };
    var uncachePointerEvent = function(event) {
        if (event.pointerId && pointerCache[event.pointerId]) {
            delete pointerCache[event.pointerId];
        }
    };
    var pointerUp = function(event) {
        uncachePointerEvent(event);
    };
    var pointerDown = function(event) {
        cachePointerEvent(event);
    };
    var pointerMove = function(event) {
        cachePointerEvent(event);
    };
    this.addEventListener = function(element, eventType, callback, useCapture) {
        if (!useCapture) {
            useCapture = false;
        }
        if (typeof handlers[eventType] !== 'undefined') {
            var handlerExists = false;

            for (var i = 0; i < handlers[eventType].length; i++) {
                if (handlers[eventType][i]['callback'] == callback && handlers[eventType][i]['element'] == element) {
                    handlerExists = true;
                }
            }
            if (!handlerExists) {
                var handlerObject = {};
                handlerObject['callback'] = callback;
                handlerObject['element'] = element;
                handlers[eventType].push(handlerObject);
            }
            if (typeof element !== 'undefined' && typeof callback !== 'undefined') {
                if (eventType == 'start') {
                    element.addEventListener(startEventName, captureStartEvent, useCapture);
                } else if (eventType == 'move') {
                    element.addEventListener(moveEventName, captureMoveEvent, useCapture);
                } else if (eventType == 'end') {
                    element.addEventListener(endEventName, captureEndEvent, useCapture);
                } else if (eventType == 'cancel') {
                    element.addEventListener(cancelEventName, captureCancelEvent, useCapture);
                }
            }
        }
    };
    this.removeEventListener = function(element, eventType, callback) {
        if (typeof handlers[eventType] !== 'undefined') {
            for (var i = handlers[eventType].length; i--;) {
                if (handlers[eventType][i]['callback'] == callback && handlers[eventType][i]['element'] == element) {
                    handlers[eventType][i] = null;
                    handlers[eventType].splice(i, 1);
                }
            }
        }
    };
    this.setTouchAction = function(element, action) {
        if (eventsSet == 'mspointer') {
            // IE10
            element.style.msTouchAction = action;
        } else {
            element.style.touchAction = action;
        }
    };
    init();
};