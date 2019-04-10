<tr class="category_products_table_product category_products_table_productid_{$element->id}">
	<script>window.products = window.products ? window.products: []</script>
	<script>window.products.push({$element->getElementData()|json_encode})</script>
	{if $columns.title}
		<td class="">
			<a href="{$element->URL}" class="category_products_table_product_link">{$element->title}</a>
		</td>
	{/if}
	{if $columns.code}
		<td class="">
			{$element->code}
		</td>
	{/if}
	{if $columns.unit}
		<td class="">
			{$element->getUnit()}
		</td>
	{/if}
	{if $columns.minimumOrder}
		<td class="">
			{$element->minimumOrder}
		</td>
	{/if}
	{if $columns.availability}
		<td class="">
			{if $element->availability == "quantity_dependent"}
				{translations name='product.instock' q=$element->quantity}
			{elseif $element->availability == "inquirable"}
				{translations name='product.inquirable'}
			{elseif $element->availability == "unavailable"}
				{translations name='product.unavailable'}
			{elseif $element->availability == "available_inquirable"}
				{translations name='product.available_inquirable'}
			{else}
				{translations name='product.available'}
			{/if}
		</td>
	{/if}
	{foreach $parameters as $parameter}
		<td>
			{if $parameterInfo = $element->getParameterInfoById($parameter['id'])}
				{if $parameterInfo.structureType == 'productParameter'}
					{$parameterInfo.value}
				{elseif $parameterInfo.structureType == 'productSelection'}
					{stripdomspaces}
					{foreach from=$parameterInfo.productOptions item=option name=options}
						{if $option.originalName}
							<img class="product_parameter_icons_item fancytitle lazy_image" src="{$theme->getImageUrl('lazy.png')}" data-lazysrc="{$controller->baseURL}image/type:productOption/id:{$option.image}/filename:{$option.originalName}" alt="{$option.title}" title="{$option.title}" />
						{else}
							{$option.title}{if !$smarty.foreach.options.last},&#32;{/if}
						{/if}
					{/foreach}
					{/stripdomspaces}
				{/if}
			{/if}
		</td>
	{/foreach}
	{if $columns.price}
		<td class="category_products_table_price_cell">
			{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}
		</td>
	{/if}
	{if $columns.discount}
		<td class="category_products_table_discount_cell">
			{if $element->getOldPrice()}
				<span class="product_discount">-{$element->getDiscountPercent()|round}%</span>
			{/if}
		</td>
	{/if}
	{if $shoppingBasket}
		{if $columns.quantity}
			<td class="category_products_table_amount_cell">
				<div class="product_short_controls" data-minimum-order="{$element->minimumOrder}">
					<div class="product_short_amount_block">
						<span class="button product_short_amount_button_minus product_short_amount_button">-</span>
						<input class='input_component product_short_amount_input' type="text" value="{if $element->minimumOrder>0}{$element->minimumOrder}{else}1{/if}" />
						<span class="button product_short_amount_button_plus product_short_amount_button">+</span>
					</div>
				</div>
			</td>
		{/if}
		{if $columns.basket}
			<td class="category_products_table_basket_cell">
				<span class="product_short_basket product_short_button category_products_table_product_button button button_small">
					<span class='button_text'>{translations name='product.addtobasket'}</span>
				</span>
			</td>
		{/if}
	{/if}
	{if $columns.view}
		<td class="category_products_table_button_cell">
			<a href="{$element->URL}" class="product_short_button category_products_table_product_button button button_small">
				<span class='button_text'>{translations name='product.select'}</span>
			</a>
		</td>
	{/if}
</tr>