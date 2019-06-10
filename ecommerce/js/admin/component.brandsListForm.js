window.BrandsListFormComponent = function(componentElement) {
    var brandsRowElement;
    var brandCheckboxElement;

    var init = function() {
        brandCheckboxElement = _('input.brandslist_connectall_checkbox', componentElement)[0];
        brandsRowElement = _('.brandslist_brands_row', componentElement)[0];
        checkBrandsRow();
        eventsManager.addHandler(brandCheckboxElement, 'change', checkBrandsRow);
    };

    var checkBrandsRow = function() {
        if (brandCheckboxElement.checked) {
            brandsRowElement.style.display = 'none';
        } else {
            brandsRowElement.style.display = '';
        }
    };
    init();
};