window.ProductsPriceFilterComponent = function(componentElement, filterData, selectorType, listComponent) {
    var barElement, startInputElement, endInputElement;
    var knobLeft, knobRight;
    var scaleSize;
    var INCREMENT_AMOUNT = 5;
    var min = 0, max = 0;
    var selectionMin = 0, selectionMax = 0;
    var changed = false;
    var applyTimeout;

    var self = this;

    var init = function() {
        createDomStructure();
        selectionMin = Math.floor(filterData.getSelectedRange()[0]);
        selectionMax = Math.ceil(filterData.getSelectedRange()[1]);
        min = Math.floor(Math.min(selectionMin, filterData.getRange()[0]));
        max = Math.ceil(Math.max(selectionMax, filterData.getRange()[1]));

        scaleSize = max - min;

        knobLeft = new ProductsFilterPriceControlKnobComponent(self, barElement, 'left');
        knobRight = new ProductsFilterPriceControlKnobComponent(self, barElement, 'right');

        selectPriceRange(selectionMin, selectionMax);

        eventsManager.addHandler(startInputElement, 'change', inputChange);
        eventsManager.addHandler(startInputElement, 'keyup', inputKeyUp);
        eventsManager.addHandler(endInputElement, 'change', inputChange);
        eventsManager.addHandler(endInputElement, 'keyup', inputKeyUp);
        eventsManager.addHandler(window, 'resize', self.refresh);
        controller.addListener('MobileCommonMenuReappended', self.refresh);
    };

    var inputKeyUp = function(event) {
        checkInputs(event.target);
    };

    var inputChange = function(event) {
        checkInputs(event.target);
    };

    var checkInputs = function(changedInput) {
        if (changedInput.value === '') {
            return;
        }
        var inputValue = +changedInput.value;
        if (inputValue !== inputValue) {
            inputValue = 0;
        }
        if (changedInput === startInputElement) {
            if (inputValue < min) {
                selectionMin = min;
            } else if (inputValue > selectionMax) {
                selectionMin = selectionMax - INCREMENT_AMOUNT * 3;
            } else {
                selectionMin = inputValue;
            }
        } else {
            if (inputValue > max) {
                selectionMax = max;
            } else if (inputValue < selectionMin) {
                selectionMax = selectionMin + INCREMENT_AMOUNT * 3;
            } else {
                selectionMax = inputValue;
            }
        }
        applyDelayedly();
    };

    var applyInputs = function() {
        listComponent.changeFilters();
    };

    var applyDelayedly = function() {
        window.clearTimeout(applyTimeout);
        applyTimeout = window.setTimeout(applyInputs, 1000);
    };

    this.refresh = function() {
        knobLeft.refresh();
        knobRight.refresh();
        selectPriceRange(selectionMin, selectionMax);
    };

    this.modifyFilterArguments = function(arguments) {
        if (selectionMin != min || selectionMax != max) {
            if (typeof arguments['price'] == 'undefined') {
                arguments['price'] = [];
            }
            arguments['price'][arguments['price'].length] = selectionMin + '-' + selectionMax;
        }
    };

    var selectPriceRange = function(startPrice, endPrice) {
        knobLeft.position((startPrice - min) / scaleSize);
        knobRight.position((endPrice - min) / scaleSize);
        knobLeft.limit(knobRight.getValue());
        knobRight.limit(knobLeft.getValue());
        startInputElement.value = startPrice;
        endInputElement.value = endPrice;
    };

    var createDomStructure = function() {
        barElement = self.makeElement('div', 'products_filter_price_control_bar', componentElement);

        var elements = _('input', componentElement);
        startInputElement = elements[0];
        endInputElement = elements[1];
    };

    this.adjustMin = function() {
        knobRight.limit(knobLeft.getValue());
        var newMin = Math.max(min, Math.floor(scaleSize * knobLeft.getValue() + min));
        if (scaleSize > INCREMENT_AMOUNT && newMin > min) {
            newMin = Math.max(min, roundAmount(newMin));
        }
        if (newMin != selectionMin) {
            changed = true;
            selectionMin = newMin;
            startInputElement.value = newMin;
        }
    };

    this.adjustMax = function() {
        knobLeft.limit(knobRight.getValue());
        var newMax = Math.min(max, Math.ceil(scaleSize * knobRight.getValue() + min));
        if (scaleSize > INCREMENT_AMOUNT && newMax < max) {
            newMax = Math.max(min, roundAmount(newMax));
        }
        if (newMax != selectionMax) {
            changed = true;
            selectionMax = newMax;
            endInputElement.value = newMax;
        }
    };

    this.knobRelease = function() {
        if (changed) {
            applyInputs();
        }
    };

    var roundAmount = function(knobAmount) {
        return scaleSize > INCREMENT_AMOUNT ? knobAmount - (knobAmount % INCREMENT_AMOUNT) : knobAmount;
    };

    this.getComponentElement = function() {
        return componentElement;
    };

    this.hide = function() {
        domHelper.addClass(componentElement, 'products_filter_price_control_hidden');
    };

    this.show = function() {
        domHelper.removeClass(componentElement, 'products_filter_price_control_hidden');
    };

    this.getValue = function() {
        return '';
    };

    this.getType = function() {
        return filterData.getType();
    };

    this.resetValue = function() {
        alert('reset')
    };

    init();
};
DomElementMakerMixin.call(ProductsPriceFilterComponent.prototype);

window.ProductsFilterPriceControlKnobComponent = function(priceControlComponent, barElement, type) {
    var value = 0.000000;

    var componentElement;
    var grasped = false;
    var locked = false;
    var graspPosition = 0;

    var offset = 0;
    var limit = 0;
    var MAX_PROXIMITY = 15;
    var scaleSize = 0;

    var self = this;

    var init = function() {
        componentElement = self.makeElement('div', 'products_filter_price_control_knob products_filter_price_control_knob_' + type, barElement);
        scaleSize = barElement.offsetWidth - componentElement.offsetWidth;
        limit = scaleSize - MAX_PROXIMITY;
        calculateOffset();
        if (type === 'right') {
            value = 1;
        }
        eventsManager.addHandler(componentElement, 'mousedown', mouseDown);
        eventsManager.addHandler(document.body, 'mouseup', mouseUp);
        eventsManager.addHandler(document.body, 'mousemove', mouseMove);
    };

    var mouseMove = function(event) {
        if (grasped && !locked) {
            eventsManager.preventDefaultAction(event);
            var dragWidth = graspPosition - event.clientX;
            if (type === 'right') {
                dragWidth *= -1;
            }
            var newOffset = offset - dragWidth;
            if (newOffset > limit) {
                newOffset = limit;
            }
            componentElement.style[type] = Math.max(newOffset, 0) + 'px';
            value = componentElement.offsetLeft / scaleSize;
            if (type === 'left') {
                priceControlComponent.adjustMin();
            } else {
                priceControlComponent.adjustMax();
            }
        }
    };

    var mouseDown = function(event) {
        eventsManager.preventDefaultAction(event);
        calculateOffset();
        graspPosition = event.clientX;
        grasped = true;
    };

    var mouseUp = function(event) {
        // update the labels and opposite knob if I was moved
        if (!locked && grasped && graspPosition !== event.clientX) {
            priceControlComponent.knobRelease();
        }
        grasped = false;
    };

    var calculateOffset = function() {
        offset = componentElement.offsetLeft;
        if (type === 'right') {
            offset = scaleSize - offset;
        }
    };

    this.limit = function(oppositeValue) {

        if (type === 'right') {
            oppositeValue = 1 - oppositeValue;
        }
        limit = Math.max(0, oppositeValue * scaleSize - MAX_PROXIMITY);
        locked = (limit <= MAX_PROXIMITY);
    };

    this.getValue = function() {
        return value;
    };

    this.position = function(newValue) {
        value = newValue;
        if (type === 'right') {
            newValue = 1 - newValue;
        }
        var position = scaleSize * newValue;
        componentElement.style[type] = position + 'px';
    };

    this.refresh = function() {
        scaleSize = barElement.offsetWidth - componentElement.offsetWidth;
        limit = scaleSize - MAX_PROXIMITY;
        calculateOffset();
    };
    init();
};
DomElementMakerMixin.call(ProductsFilterPriceControlKnobComponent.prototype);