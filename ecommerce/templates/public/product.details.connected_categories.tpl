{if count($element->getProductConnectedCategories())}
	{$connectedProductsFromCategories = $element->getShuffledProductFromConnectedCategories()}
	{include file=$connectedTemplate products=$connectedProductsFromCategories title="{translations name='product.connectedproductsfromcategories'}"}
{/if}
