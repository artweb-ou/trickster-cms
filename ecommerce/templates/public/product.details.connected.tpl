{if $products}
	{$layout = $element->getDefaultLayout('connected')}
	<div class='product_details_connected'>
		<h2 class="product_details_connected_heading">{$title}</h2>
		<div class="product_details_connected_products productslist_products">
			{foreach $products as $product}
				{include file=$theme->template("product.$layout.tpl") element=$product}
			{/foreach}
		</div>
	</div>
{/if}