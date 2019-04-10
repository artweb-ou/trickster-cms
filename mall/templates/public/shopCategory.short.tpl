{stripdomspaces}
{if empty($shopLayout)}
	{$shopLayout = 'thumbnail'}
{/if}
<div class="shopcategory_short shopcategory_short_shoplayout_{$shopLayout}">
	{include file=$theme->template('shopCategory.header.tpl')}
	<div class="shopcategory_short_shops">
		{foreach $element->getShopsList() as $shop}
			{include file=$theme->template("shop.{$shopLayout}.tpl") element=$shop}
		{/foreach}
	</div>
</div>
{/stripdomspaces}