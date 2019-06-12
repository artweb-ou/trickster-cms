window.DiscountsListFormComponent = function(componentElement) {
    var discountsRowElement;
    var discountCheckboxElement;

    var init = function() {
        discountCheckboxElement = _('input.discountslist_connectall_checkbox', componentElement)[0];
        discountsRowElement = _('.discountslist_discounts_row', componentElement)[0];
        checkDiscountsRow();
        eventsManager.addHandler(discountCheckboxElement, 'change', checkDiscountsRow);
    };

    var checkDiscountsRow = function() {
        if (discountCheckboxElement.checked) {
            discountsRowElement.style.display = 'none';
        } else {
            discountsRowElement.style.display = '';
        }
    };
    init();
};