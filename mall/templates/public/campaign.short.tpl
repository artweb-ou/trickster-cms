<div class="campaign_short clickable_component">
	<div class="campaign_short_inner">

		<div class="campaign_short_image_wrap">
			{if $element->originalName}
				<img class="campaign_short_image" src='{$controller->baseURL}image/type:campaignShortImage/id:{$element->image}/filename:{$element->originalName}' alt="{$element->title}"/>
			{else}
				<img class="campaign_short_image" src="{$theme->getImageUrl('campaign_default.jpg')}" alt="{$element->title}"/>
			{/if}
		</div>

		{$shop = $element->getConnectedShop()}
		<div class="campaign_short_title">
			{if $shop}
				<a class="campaign_short_shop" href="{$shop->URL}">
					{$shop->title}
				</a>
				{if $mapUrl = $shop->getMapUrl()}
					<a class="campaign_short_location" href="{$mapUrl}">
						{translations name='campaign.shop_location'}
					</a>
				{/if}
			{/if}

			<a class="campaign_short_link" href="{$element->URL}">
				{$element->title}
			</a>
			<div class="clearfix"></div>
		</div>

		<div class='campaign_short_content'>
			{$element->introduction}
		</div>
	</div>
</div>