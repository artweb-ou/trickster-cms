{debug}
{if $products}
	{$layout = $element->getDefaultLayout('connected')}

	{if isset($products[4])}
	<div class="selected_products_block">
		<div class="">
			<div class='product_details_connected selectedproducts_content selectedproducts_content_scrolltype'>
				<div class="selectedproducts_scroll" data-auto="1">
                    {foreach $products as $product}
                        {include file=$theme->template("product.$layout.tpl") element=$product}
                    {/foreach}
				</div>
				<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_left scroll_pages_previous"></div>
				<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_right scroll_pages_next"></div>
			</div>
		</div>
	</div>
	{else}
		<div class='product_details_connected'>
			<h2 class="product_details_connected_heading">{$title}</h2>
			<div class="selectedproducts_content selectedproducts_content_scrolltype">

				<div class="selected_products_container products_list">
                    {foreach $products as $product}
                        {include file=$theme->template("product.$layout.tpl") element=$product selectedProductsElement=$element}
                    {/foreach}
				</div>
			</div>
		</div>
	{/if}
{/if}