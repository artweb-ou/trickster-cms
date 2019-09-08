{assign moduleTitle $element->title}
{capture assign="moduleSideContent"}
	{if $element->originalName != ""}
		{include file=$theme->template('component.elementimage.tpl') type='productWide' class='product_wide_image' lazy=true}
	{/if}
	{if $iconsInfo = $element->getIconsInfo()}
		{include file=$theme->template('product.icons.tpl') class='product_wide_icons'}
	{/if}
{/capture}
{capture assign="moduleContent"}
	<div class="product_wide_price">{if !$element->isEmptyPrice()}{translations name='product.price'}: {$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</div>
	<div class="product_wide_code">{translations name='product.code'}: {$element->code}</div>
	<div class="product_wide_content">
		{if $element->introduction}
			<div class="product_wide_description">
				{$element->introduction}
			</div>
		{/if}

		{assign "primaryParametersInfo" $element->getPrimaryParametersInfo()}
		{if $primaryParametersInfo}
			<table class='product_wide_parameters'>
				<tr>
					{assign "iteration" 1}

					{foreach from=$primaryParametersInfo item=parameterInfo}
						<td class="product_wide_parameter">
							<span class="product_wide_parameter_title">{$parameterInfo.title}:</span>
							<span class="product_wide_parameter_value">
								{include file=$theme->template("product.details.parameters.options.tpl") parameterInfo = $parameterInfo}
							</span>
						</td>

						{if $iteration is div by 3}
							</tr><tr>
						{/if}

						{assign var=iteration value=$iteration+1}
					{/foreach}
				</tr>
			</table>
		{/if}
	</div>
{/capture}
{capture assign="moduleControls"}
	<a href="{$element->URL}" class="product_wide_button product_short_button button">
		<span class='button_text'>{translations name='product.short_select'}</span>
	</a>

	{if $shoppingBasket && $element->isPurchasable() && !$element->isBasketSelectionRequired()}
		<div class="product_short_controls" data-minimum-order="{$product->minimumOrder}">
			<div class="product_short_amount_block">
				<span class="button product_short_amount_button_minus product_short_amount_button">-</span>
				<input class='input_component product_short_amount_input' type="text" value="{if $element->minimumOrder>0}{$element->minimumOrder}{else}1{/if}" />
				<span class="button product_short_amount_button_plus product_short_amount_button">+</span>
			</div>
			<span class="product_wide_button product_short_basket product_short_button button"><span class='button_text'>{translations name='product.short_addtobasket'}</span></span>
		</div>
	{/if}
{/capture}
{assign moduleClass "product_wide product_short productid_{$element->id}"}
{assign moduleTitleClass "product_wide_title"}
{assign moduleAttributes ''}
{assign moduleSideContentClass "product_wide_image_container"}
{assign moduleControlsClass "product_wide_controls"}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}