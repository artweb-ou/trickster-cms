{capture assign="moduleContent"}
	{if $element->photo}
		<div class="shop_details_image_container">
			<img class="shop_details_image" src="{$controller->baseURL}image/type:shopShortPhoto/id:{$element->photo}/filename:{$element->photoOriginalName}" alt="{$element->title}"/>
		</div>
	{/if}
	<div class="shop_details_description">
		<h1 class='shop_details_title'>
			{if $h1 = $element->getH1()}
				{$h1}
			{elseif $element->title}
				{$element->title}
			{/if}
		</h1>
		{$openingHours = $element->getOpeningHoursInfo()}
		{if $element->image || $openingHours || $element->openedTime || $element->contactInfo}
			<div class="shop_details_details">
				<div class="shop_details_details_inner">
					{if $element->image}
						<div class="shop_details_logo_wrap">
							<div class="shop_details_logo" style="background-image: url('{$controller->baseURL}image/type:shopShortLogo/id:{$element->image}/filename:{$element->imageOriginalName}')"></div>
						</div>
					{/if}
					{if $openingHours}
						<div class="shop_details_openedtime">
							{foreach $openingHours as $periodInfo}
								<p>
									{$periodInfo.name} {$periodInfo.times}
								</p>
							{/foreach}
						</div>
					{elseif $element->openedTime}
						<div class='shop_details_openedtime'>
							{$element->openedTime}
						</div>
					{/if}
					{if $element->contactInfo}
						<div class='shop_details_contactinfo'>
							{$element->contactInfo}
						</div>
					{/if}
					<div class="clearfix"></div>
				</div>
			</div>
		{/if}
		{if $element->content || $element->introduction}
			<div class='shop_details_text content_item'>
				{$element->content|default:$element->introduction}
			</div>
		{/if}
	</div>
	<div class="clearfix"></div>
	{if $images = $element->getImagesList()}
		<div class="shop_details_gallery gallery_details">
			<script>

				window.galleriesInfo = window.galleriesInfo || {ldelim}{rdelim};
				window.galleriesInfo['{$element->id}'] = {$element->getGalleryJsonInfo(true, true, true)};

			</script>
			{stripdomspaces}
			<div class="gallery_details_images gallery_static galleryid_{$element->id}">
				{foreach $element->getImagesList() as $image}
					{include file=$theme->template($image->getTemplate()) element=$image}
				{/foreach}
			</div>
			{/stripdomspaces}
		</div>
	{/if}
	{if $mapUrl = $element->getMapUrl()}
		<div class="shop_details_controls">
			<a class="shop_details_location_link button" href="{$mapUrl}">
				{translations name='shop.location'}
			</a>
		</div>
	{/if}
	{if $element->final}
		{if $campaigns = $element->getCampaigns()}
			<div class="shop_details_campaigns">
				{foreach $campaigns as $campaign}
					{include file=$theme->template("campaign.bar.tpl") element=$campaign}
				{/foreach}
			</div>
		{/if}
	{/if}
{/capture}
{assign moduleClass "shop_details"}
{assign moduleContentClass "shop_details_content"}
{include file=$theme->template("component.contentmodule.tpl")}