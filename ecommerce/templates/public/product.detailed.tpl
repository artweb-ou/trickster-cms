{assign moduleTitle $element->title}
{capture assign="moduleSideContent"}
		{if $element->originalName != ""}
			<div>
				{include file=$theme->template('component.elementimage.tpl') type='productDetailed' class='product_detailed_image' lazy=true}
			</div>
		{/if}
		{$icons=$element->getIconsCompleteList()}
		{$connectedDiscounts=$element->getCampaignDiscounts()}
		{if $icons || $connectedDiscounts}
			<div class="product_detailed_icons">
				{if $element->getOldPrice()}
					{if $discount = $element->getDiscountPercent()}
						<div class="product_discount_container">
							<span class="product_discount">
								-{$discount}%
							</span>
						</div>
					{/if}
				{/if}
				{foreach $icons as $icon}
					<img class='product_detailed_icons_image lazy_image' src="{$theme->getImageUrl('lazy.png')}" data-lazysrc='{$controller->baseURL}image/type:productIcon/id:{$icon->image}/filename:{$icon->originalName}' alt='{$icon->title}'{if $icon->iconWidth > 0} style="max-width: {$icon->iconWidth}%; width: auto; max-height: none; height: auto;"{/if}/>
				{/foreach}

				{foreach from=$connectedDiscounts item=discount}
					{if $discount->icon}
						<img class='product_detailed_icons_image lazy_image' src="{$theme->getImageUrl('lazy.png')}" data-lazysrc='{$controller->baseURL}image/type:productIcon/id:{$discount->icon}/filename:{$discount->iconOriginalName}' alt='{$discount->title}'{if $discount->iconWidth > 0} style="max-width: {$discount->iconWidth}%; width: auto; max-height: none; height: auto;"{/if} />
					{/if}
				{/foreach}
			</div>
		{/if}
{/capture}
{capture assign="moduleContent"}
	<div class="product_detailed_price">{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</div>
	{assign "primaryParametersInfo" $element->getPrimaryParametersInfo()}
	{if $primaryParametersInfo}
		<table class='product_detailed_parameters'>
			{foreach from=$primaryParametersInfo item=parameterInfo}
				<tr {if $smarty.foreach.parametersList.iteration is odd}class='product_detailed_parameter_odd'{/if}>
					{if $parameterInfo.title != ''}
						{stripdomspaces}
						<td class="product_detailed_parameter_key">
							{$parameterInfo.title}{if $parameterInfo.originalName != ''}<img class='product_detailed_parameter_image lazy_image' src="{$theme->getImageUrl('lazy.png')}" data-lazysrc='{$controller->baseURL}image/type:productParameter/id:{$parameterInfo.image}/filename:{$parameterInfo.originalName}' alt='{$parameterInfo.title}' />{/if}:
						</td>
						{/stripdomspaces}
					{/if}
					<td class="product_detailed_parameter_value" {if $parameterInfo.title == ''}colspan="2"{/if}>
						{if $parameterInfo.structureType == 'productParameter'}
							{$parameterInfo.value}
						{elseif $parameterInfo.structureType == 'productSelection'}
							{stripdomspaces}
							{foreach from=$parameterInfo.productOptions item=option name=options}
								{if $option.originalName}
									<img class="product_parameter_icons_item fancytitle lazy_image" src="{$theme->getImageUrl('lazy.png')}" data-lazysrc="{$controller->baseURL}image/type:productOption/id:{$option.image}/filename:{$option.originalName}" alt="{$option.title}" title="{$option.title}" />
								{else}
									{$option.title}{if !$smarty.foreach.options.last},&#32;{/if}
								{/if}
							{/foreach}
							{/stripdomspaces}
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	{/if}

	{if $element->introduction}
		<div class="product_detailed_introduction">
			{$element->introduction}
		</div>
	{/if}

	{assign "basketSelectionsInfo" $element->getBasketSelectionsInfo()}
	{if $shoppingBasket && $element->isPurchasable() && $element->isBasketSelectionRequired()}
		{stripdomspaces}
			<div class="product_detailed_options">
				{foreach $basketSelectionsInfo as $selectionInfo}
					<div class="product_detailed_option">
						{if $selectionInfo.title}
							<div class="product_detailed_option_title">
								{$selectionInfo.title}:
							</div>
						{/if}
						<select class="product_detailed_option_select product_short_option_select dropdown_placeholder">
							{foreach $selectionInfo.productOptions as $value}
								<option value='{$selectionInfo.title}: {$value.title}'>{$value.title}</option>
							{/foreach}
						</select>
					</div>
				{/foreach}
			</div>
		{/stripdomspaces}
	{/if}
	<script>window.products = window.products ? window.products : []</script>
	<script>window.products.push({$element->getElementData()|json_encode})</script>
{/capture}

{capture assign="moduleControls"}
	{if $shoppingBasket && $element->isPurchasable()}
		<a href="{$element->URL}" class="product_short_basket product_short_button product_detailed_button button">
			<span class='button_text'>{translations name='product.addtobasket'}</span>
		</a>
	{/if}
	<a href="{$element->URL}" class="product_short_link product_short_button product_detailed_button button">
		<span class='button_text'>{translations name='product.readmore'}</span>
	</a>
{/capture}

{assign moduleClass "product_detailed productid_{$element->id}"}
{assign moduleTitleClass "product_detailed_header_title"}


{assign moduleSideContentClass "product_wide_image_container"}
{assign moduleContentClass "product_detailed_column_right"}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}
