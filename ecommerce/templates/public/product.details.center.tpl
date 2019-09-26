<div class="product_details_center">
	{* CONTENT *}
	{if $element->content}
		<div class='product_details_description html_content'>{$element->content}</div>
	{/if}
	{$subArticles = $element->getSubArticles()}
	{if !empty($subArticles)}
		<div class="article_subarticles">
			{foreach $subArticles as $subArticle}
				{include file=$theme->template($subArticle->getTemplate('simple')) element=$subArticle}
			{/foreach}
		</div>
	{/if}
	{include $theme->template('product.details.files.tpl')}

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
