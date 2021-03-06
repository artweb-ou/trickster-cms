{if $element->isBasketSelectionRequired()}
	{stripdomspaces}
		<div class="product_details_options">
			{foreach from=$element->getBasketSelectionsInfo() item=selectionInfo}
				<div class="product_details_option">
					{if $selectionInfo.title}
						<div class="product_details_option_title">
							<span class="product_details_option_title_value">{$selectionInfo.title}:</span>
							{if !empty($selectionInfo.hint)}
								<div class="product_details_option_hint"></div>
							{/if}
						</div>
					{/if}
					<div class="product_details_option_control" data-elementid="{$selectionInfo.id}" data-influential="{$selectionInfo.influential}">
					<div class="option_items_wrapper">
						{if $selectionInfo.controlType == 'radios'}
							{foreach from=$selectionInfo.productOptions key=valueKey item=value}
								<div data-product-details="option_item" class="product_details_option_radio_item">
									<input class="radio_holder product_details_option_radio_item_control" type="radio" name="product_selection_option[{$selectionInfo.id}]" id="product_selection_option_{$value.id}" value="{$value.id}" {if $valueKey === 0}checked{/if} />
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
						<div data-product-details="option_item" class="product_details_option_select_item">
							<select data-product-details="option_item" class="product_details_option_selector product_selection_{$selectionInfo.id} dropdown_placeholder">
								{foreach $selectionInfo.productOptions as $value}
									<option value='{$value.id}'>{$value.title}</option>
								{/foreach}
							</select>
						</div>
						{/if}
					</div>
					</div>
				</div>
			{/foreach}
		</div>
	{/stripdomspaces}
{/if}