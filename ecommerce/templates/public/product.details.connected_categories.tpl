{if $connectedProductsFromCategories = $element->getShuffledProductFromConnectedCategories()}
	{$connectedTemplate = $theme->template('product.details.connected.tpl')}
	{include file=$connectedTemplate products=$connectedProductsFromCategories title="{translations name='product.connectedproductsfromcategories'}"}
{/if}
