<div class="shop_thumbnail">
	<a class="shop_thumbnail_link" href="{$element->URL}" title="{$element->title}">
		<span class="shop_thumbnail_link_inner">
			<span class="shop_thumbnail_image_wrap">
				{if $element->image}
					<img class="shop_thumbnail_image" src="{$controller->baseURL}image/type:shopThumbnail/id:{$element->image}/filename:{$element->originalName}" alt="{$element->title}"/>
				{/if}
			</span>
			<span class="shop_thumbnail_title">
				{$element->title}
			</span>
		</span>
	</a>
</div>