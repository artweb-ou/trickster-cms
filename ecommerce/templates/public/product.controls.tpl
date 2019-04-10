<div class="product_details_controls">
	{include file=$theme->template("product.controls.prices.tpl")}

	{if $element->isPurchasable()}
		{if $element->minimumOrder>1}
			<div class="product_details_minimumorder">{translations name="product.minimumorder"}: <span class="product_minimumorder_value">{$element->minimumOrder}</span></div>
		{/if}
		<div class="product_details_controls_buttons">
			<div class="product_details_amount_block">
				<span class="button product_details_amount_minus product_details_amount"><span class="button_text">-</span></span>
				<!--suppress HtmlFormInputWithoutLabel -->
				<input class='input_component product_details_amount_input' type="text" value="{if $element->minimumOrder>0}{$element->minimumOrder}{else}1{/if}" />
				<span class="button product_details_amount_plus product_details_amount"><span class="button_text">+</span></span>
			</div>
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