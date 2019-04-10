<section class="campaign_thumbnail">
	<a href="{$element->URL}" class="campaign_thumbnail_link">
		<span class="campaign_thumbnail_image_container">
				<img class="campaign_thumbnail_image" src='{$controller->baseURL}image/type:campaignThumbnail/id:{$element->image}/filename:{$element->originalName}' alt="{$element->title}"/>
		</span>
		{$shop = $element->getConnectedShop()}
		<span class="campaign_thumbnail_title">
			{if $shop}
				{$shop->title}
			{else}
				{$element->title}
			{/if}
		</span>
		{if $shop}
			<span class="campaign_thumbnail_introduction">
				{$element->title}
			</span>
		{/if}
	</a>
</section>