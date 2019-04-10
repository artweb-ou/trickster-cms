<div class="campaign_bar">
	<div class="campaign_bar_inner">
		{if $element->image}
			<div class="campaign_bar_image_wrap">
				<img class="campaign_bar_image" src="{$controller->baseURL}image/type:campaignBar/id:{$element->image}/filename:{$element->originalName}" alt="{$element->title}" />
			</div>
		{/if}
		<div class="campaign_bar_content">
			<h3 class="campaign_bar_title">
				{$element->title}
			</h3>
			{if $element->introduction}
				<div class="campaign_bar_description html_content">
					{$element->introduction}
				</div>
			{/if}
		</div>
		<div class="clearfix"></div>
	</div>
</div>