<a class="shop_line" href="{if empty($useMapUrls)}{$element->URL}{else}{$element->getMapUrl()}{/if}">
	<div class="shop_line_category">
		{if $category = $element->getMainCategory()}{$category->title}{/if}
	</div>
	<div class="shop_line_main">
		<span class="shop_line_title">
			{$element->title}
		</span>
		<div class="shop_line_floor">
			{if $floor = $element->getFloor()}{$floor->title}{/if}
		</div>
	</div>
</a>