<div class="product_details_controls">
	{include file=$theme->template("product.controls.prices.tpl")}

	{if $element->isPurchasable()}
		{if $element->minimumOrder>1}
			<div class="product_details_minimumorder">{translations name="product.minimumorder"}: <span class="product_minimumorder_value">{$element->minimumOrder}</span></div>
		{/if}
		<div class="product_details_controls_buttons">
			{include file=$theme->template('element.productAmountControlsBlock.tpl') additionalClass='product_details' element=$element inputAmount="{if $element->minimumOrder>0}{$element->minimumOrder}{else}1{/if}"}
			<span class="product_details_button button">
				<span class="button_icon"></span>
				<span class='button_text'>{translations name='product.addtobasket'}</span>
			</span>
		</div>
	{else}
		{if $element->availability == "inquirable" && $element->getInquiryForm()}
			<div class="product_details_inquiry">
				<a class="product_details_inquiry_link" href="{$element->getInquiryForm()->URL}product:{$element->id}/">{translations name='product.sendquestion'}</a>
			</div>
		{else}
			<div class="product_details_unavailable_msg">
				{translations name='product.unavailable'}
			</div>
		{/if}
	{/if}
</div>