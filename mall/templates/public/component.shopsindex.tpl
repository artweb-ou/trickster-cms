{if empty($useMapUrls)}
	{$useMapUrls = false}
{/if}

{if empty($shopLayout)}
	{$shopLayout = 'line'}
{/if}
<div class="shops_index shops_index_shoplayout_{$shopLayout}">
	{foreach $index as $shops}
		<div class="shops_index_section">
			<div class="shops_index_letter">
				{$shops@key}
			</div>
			<div class="shops_index_shops">
				{foreach $shops as $shop}
					{include file=$theme->template("shop.{$shopLayout}.tpl") element=$shop}
				{/foreach}
			</div>
		</div>
	{/foreach}
</div>