{assign var='banners' value=$element->getBannersToDisplay()}
{stripdomspaces}
{if $banners}
	<div class="bannercategory_block bannercategory_footer{if $element->marker} bannercategory_{$element->marker}{/if}">
		{foreach from=$banners item=banner}
			{include file=$theme->template('banner.show.tpl') element=$banner}
		{/foreach}
	</div>
{/if}
{/stripdomspaces}