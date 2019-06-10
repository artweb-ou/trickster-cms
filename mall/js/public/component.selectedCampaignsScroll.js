window.SelectedCampaignsScrollComponent = function(componentElement) {
    var self = this;
    var init = function() {
        self.spInit({'componentElement': componentElement, 'resizeRequired': true});
        eventsManager.addHandler(componentElement, 'mouseenter', mouseEnter);
        eventsManager.addHandler(componentElement, 'mouseleave', mouseLeave);
    };
    var mouseEnter = function(event) {
        self.spStopRotation();
    };
    var mouseLeave = function(event) {
        self.spStartRotation();
    };
    init();
};
ScrollPagesMixin.call(SelectedCampaignsScrollComponent.prototype);