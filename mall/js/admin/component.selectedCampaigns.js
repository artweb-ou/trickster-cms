window.SelectedCampaignsComponent = function(componentElement) {
    var campaignsRowElement;
    var campaignCheckboxElement;

    var init = function() {
        campaignCheckboxElement = _('input.selectedcampaigns_connectall_checkbox', componentElement)[0];
        campaignsRowElement = _('.selectedcampaigns_campaigns_row', componentElement)[0];
        checkCampaignsRow();
        eventsManager.addHandler(campaignCheckboxElement, 'change', checkCampaignsRow);
    };
    var checkCampaignsRow = function() {
        if (campaignCheckboxElement.checked) {
            campaignsRowElement.style.display = 'none';
        } else {
            campaignsRowElement.style.display = '';
        }
    };
    init();
};