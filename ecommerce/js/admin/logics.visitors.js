window.logicsVisitors = new function() {

	var init = function() {
		var visitor_category_ajaxselect = document.querySelector('.visitor_category_ajaxselect');
		var visitor_product_ajaxselect = document.querySelector('.visitor_product_ajaxselect');

		new AjaxSelectComponent(visitor_category_ajaxselect, "category", "admin");
		new AjaxSelectComponent(visitor_product_ajaxselect, "product", "admin");
	};

	controller.addListener('initDom', init);
};