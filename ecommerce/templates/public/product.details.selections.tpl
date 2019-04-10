{if $element->isBasketSelectionRequired()}
	{stripdomspaces}
		<div class="product_details_options">
			{foreach from=$element->getBasketSelectionsInfo() item=selectionInfo}
				<div class="product_details_option">
					{if $selectionInfo.title}
						<div class="product_details_option_title">
							{$selectionInfo.title}:
						</div>
					{/if}
					<div class="product_details_option_control" data-elementid="{$selectionInfo.id}" data-influential="{$selectionInfo.influential}">
						{if $selectionInfo.controlType == 'radios'}
							{foreach $selectionInfo.productOptions as $value}
								<div class="product_details_option_radio_item">
									<input class="radio_holder product_details_option_radio_item_control" type="radio" name="product_selection_option[{$selectionInfo.id}]" id="product_selection_option_{$value.id}" value="{$value.id}" />
									{if $value.image}
										<label class="product_details_option_label" for="product_selection_option_{$value.id}">
											<img class="product_details_option_image" src="{$controller->baseURL}image/type:productSelection/id:{$value.image}/filename:{$value.originalName}" alt="{$value.title}" title="{$value.title}" />
										</label>
									{else}
										<label class="product_details_option_label" for="product_selection_option_{$value.id}">
											{if $selectionInfo.type == 'color'}
												<span class="product_details_option_color" style="background-color: #{$value.value}"></span>{/if}
											{$value.title}</label>
									{/if}
								</div>
							{/foreach}
						{else}
							<select class="product_details_option_selector product_selection_{$selectionInfo.id} dropdown_placeholder">
								{foreach $selectionInfo.productOptions as $value}
									<option value='{$value.id}'>{$value.title}</option>
								{/foreach}
							</select>
						{/if}
					</div>
				</div>
			{/foreach}
		</div>
	{/stripdomspaces}
{/if}