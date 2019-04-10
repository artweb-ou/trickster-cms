<div class="shop_short_block clickable_component">
	<h2 class='shop_short_title'>
		{$element->title}
	</h2>
	<div class='shop_short_content'>
			{if $element->photo}
				<div class="shop_short_image_container">
					<img class="shop_short_image" src="{$controller->baseURL}image/type:shopShortPhoto/id:{$element->photo}/filename:{$element->photoOriginalName}" alt="{$element->title}"/>

				</div>
			{/if}
			<div class="shop_short_description">
				<div>
					{if $element->introduction}
						<div class="shop_short_text_container">
							<div class='shop_short_text content_item'>
								{$element->introduction}
							</div>
						</div>
					{/if}
					{$openingHours = $element->getOpeningHoursInfo()}
					{if $element->image || $openingHours || $element->openedTime || $element->contactInfo}
						<div class="shop_short_details">
							<div class="shop_short_details_inner">
								{if $element->image}
									<div class="shop_short_logo_wrap">
										<div class="shop_short_logo" style="background-image: url('{$controller->baseURL}image/type:shopShortLogo/id:{$element->image}/filename:{$element->imageOriginalName}')"></div>
									</div>
								{/if}
								{if $openingHours}
									<div class="shop_short_openedtime">
										{foreach $openingHours as $periodInfo}
											<p>
												{$periodInfo.name} {$periodInfo.times}
											</p>
										{/foreach}
									</div>
								{elseif $element->openedTime}
									<div class='shop_short_openedtime'>
										{$element->openedTime}
									</div>
								{/if}
								{if $element->contactInfo}
									<div class='shop_short_contactinfo'>
										{$element->contactInfo}
									</div>
								{/if}
								<div class="clearfix"></div>
							</div>
						</div>
					{/if}
				</div>
				<div class="shop_short_readmore">
					<a href="{$element->URL}" class='shop_short_readmore_button button'>
						<span class='button_text'>{translations name='shop.readmore'}</span>
					</a>
				</div>
			</div>
			<div class="clearfix"></div>
	</div>
</div>