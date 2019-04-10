<div class="product_details_center">
	{* CONTENT *}
	{if $element->content}
		<div class='product_details_description html_content'>{$element->content}</div>
	{/if}
	{if $element->getFilesList()}
		<div class="productdetails_files">
			{foreach $element->getFilesList() as $fileElement}
				{if $fileElement->fileName != ''}
					<a href="{$controller->baseURL}file/id:{$fileElement->file}/filename:{$fileElement->fileName}" class="productdetails_file">
						{$fileElement->title}
					</a>
				{/if}
			{/foreach}
		</div>
	{/if}
	{*dicounts reference text*}
	{$connectedDiscounts=$element->getCampaignDiscounts()}
	{if $connectedDiscounts}
		{foreach from=$connectedDiscounts item=discount}
			{if $discount->reference}
				<div class="product_details_reference_text html_content">
					{$discount->reference}
				</div>
			{/if}
		{/foreach}
	{/if}
	{if $element->getInquiryForm()}
		<a class="product_details_inquiry_link_bottom" href="{$element->getInquiryForm()->URL}product:{$element->id}/">{translations name='product.sendquestion'}</a>
	{/if}
	{*{if isset($currentElementPrivileges.comment)}*}
	{*{include $theme->template('component.comments.tpl')}*}
	{*{/if}*}
</div>
