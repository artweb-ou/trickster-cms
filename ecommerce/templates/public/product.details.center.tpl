<div class="product_details_center">
	{* CONTENT *}
	{$subArticles = $element->getSubArticles()}
	{if !empty($subArticles)}
		<div class="article_subarticles">
            {foreach $subArticles as $subArticle}
				<div class="subarticle spoiler_component">
					<div class="subarticle_title spoiler_component_title">{$element->title}</div>
					<div class="subarticle__content_wrapper spoiler_component_content_wrapper">
						<div class='subarticle_content html_content spoiler_component_content'>{$element->content}</div>
					</div>
				</div>
            {/foreach}
		</div>
	{/if}
	{if $element->content}
		<div class='product_details_description html_content'>{$element->content}</div>
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
