{assign var='banners' value=$element->getBannersToDisplay()}
{if $banners}
	<div class="bannercategory_block bannercategory_column{if $element->marker} bannercategory_{$element->marker}{/if}">
		<div class="bannercategory_content">
			{foreach from=$banners item=banner}
				{include file=$theme->template('banner.show.tpl') element=$banner}
			{/foreach}
		</div>
	</div>
{/if}