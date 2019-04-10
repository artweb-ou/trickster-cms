window.productShortLogics = new function() {
	var initComponents = function() {
		var elements, i;
		elements = document.querySelectorAll('.product_gallery, .product_thumbnail, .product_mini');
		for (i = 0; i < elements.length; i++) {
			new ProductGalleryComponent(elements[i]);
		}
		elements = document.querySelectorAll('.product_detailed, .product_wide, .category_products_table_product, .product_short');
		for (i = 0; i < elements.length; i++) {
			new ProductShortComponent(elements[i]);
		}
	};
	controller.addListener('initDom', initComponents);
};