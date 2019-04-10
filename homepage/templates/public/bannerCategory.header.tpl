{assign var='banners' value=$element->getBannersToDisplay()}
{if $banners}
	<div class="header_bannercategory_block{if $element->marker} bannercategory_{$element->marker}{/if}">
		{foreach from=$banners item=banner}
			{include file=$theme->template('banner.show.tpl') element=$banner}
		{/foreach}
	</div>
{/if}