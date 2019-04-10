{stripdomspaces}
<div class="roomsmap_block">
	<div class="roomsmap_roominfo shop_short_block">
		<button type="button" class="roomsmap_roominfo_close_button" title="Close"></button>
		<div class='shop_short_content'>
			<div class="roomsmap_roominfo_content">
				<div class="roomsmap_roominfo_content_inner">
					<div class="shop_short_image_container">
					</div>
					<div class="shop_short_description">

						<h2 class='shop_short_title'></h2>
						<div class="shop_short_details">
							<div class="shop_short_details_inner">
								<div class="shop_short_logo_wrap"></div>
								<div class='shop_short_openedtime'></div>
								<div class='shop_short_contactinfo'></div>
								<div class="clearfix"></div>
							</div>
						</div>

						<div class="shop_short_text_container">
							<div class='shop_short_text content_item'></div>
							<div class="shop_short_readmore">
								<a href="#" class='shop_short_readmore_button button'>
									<span class='button_text'>{translations name='shop.readmore'}</span>
								</a>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="shop_short_campaigns"></div>
			</div>
		</div>
	</div>
	<div class="roomsmap_floors_selector"></div>
	<div class="roomsmap_floors"></div>
	<div class="roomsmap_icons">
		{foreach $element->getIcons() as $icon}
			<div class="roomsmap_icons_item">
				<img class="roomsmap_icons_item_image" src="{$controller->baseURL}image/type:roomsMapIcon/id:{$icon->image}/filename:{$icon->originalName}"/>
				<div class="roomsmap_icons_item_title">
					{$icon->title}
				</div>
			</div>
		{/foreach}
	</div>
	{if $shopsAlfaIndex = $element->getShopsAlfaIndex()}
		{include file=$theme->template("component.shopsindex.tpl") index=$shopsAlfaIndex useMapUrls=true}
	{/if}
	<div class=""></div>
</div>
{/stripdomspaces}
<script>
	/*<![CDATA[*/
		window.shopDefaultImage = '{$theme->getImageUrl('shop_default.jpg', false, false)}';
		window.roomsMapInfo = {$element->getInfo()|json_encode};
	/*]]>*/
</script>