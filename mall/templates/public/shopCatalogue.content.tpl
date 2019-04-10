<div class="shop_catalogue">{stripdomspaces}
	<div class="shop_catalogue_layout">
		<span class="shop_catalogue_layout_title">
			{translations name='shopcatalogue.shops_layout'}:
		</span>
		<a class="shop_catalogue_layout_option shop_catalogue_layout_option_list" href="#">
			<span class="shop_catalogue_layout_option_icon"></span>
		</a>
		<a class="shop_catalogue_layout_option shop_catalogue_layout_option_thumbnails" href="#">
			<span class="shop_catalogue_layout_option_icon"></span>
		</a>
		<a class="shop_catalogue_layout_option shop_catalogue_layout_option_details" href="#">
			<span class="shop_catalogue_layout_option_icon"></span>
		</a>
	</div>{/stripdomspaces}
	<div class="shop_catalogue_shops">
		{if !$controller->getParameter('letter')}
			<div class="shop_catalogue_shops_section shop_catalogue_shops_index">{stripdomspaces}
				{foreach $element->getFilteredCategories() as $category}
					{if $category->getShopsList()}
						{include file=$theme->template("shopCategory.short.tpl") element=$category shopLayout="line"}
					{/if}
				{/foreach}
			</div>
			<div class="shop_catalogue_shops_section shop_catalogue_shops_thumbnails">
				{foreach $element->getFilteredCategories() as $category}
					{if $category->getShopsList()}
						{include file=$theme->template("shopCategory.short.tpl") element=$category shopLayout="thumbnail"}
					{/if}
				{/foreach}
			</div>
		{/stripdomspaces}
			<div class="shop_catalogue_shops_section shop_catalogue_shops_details">
				{foreach $element->getFilteredCategories() as $category}
					{if $category->getShopsList()}
						{include file=$theme->template("shopCategory.short.tpl") element=$category shopLayout="short"}
					{/if}
				{/foreach}
			</div>
		{else}{stripdomspaces}
			<div class="shop_catalogue_shops_section shop_catalogue_shops_index">
				{include file=$theme->template("component.shopsindex.tpl") index=$element->getFilteredIndex() shopLayout="line"}
			</div>
			<div class="shop_catalogue_shops_section shop_catalogue_shops_thumbnails">
				{include file=$theme->template("component.shopsindex.tpl") index=$element->getFilteredIndex() shopLayout="thumbnail"}
			</div>
		{/stripdomspaces}
			<div class="shop_catalogue_shops_section shop_catalogue_shops_details">
				{include file=$theme->template("component.shopsindex.tpl") index=$element->getFilteredIndex() shopLayout="short"}
			</div>
		{/if}

	</div>
</div>