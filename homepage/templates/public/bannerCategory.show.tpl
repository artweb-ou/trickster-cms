{assign var='banners' value=$element->getBannersToDisplay()}
{if $banners}
	{capture assign="moduleContent"}
		{foreach from=$banners item=banner}
			{include file=$theme->template('banner.show.tpl') element=$banner}
		{/foreach}
	{/capture}
	{assign moduleClass "bannercategory_block{if $element->marker} bannercategory_{$element->marker}{/if}"}
	{assign moduleContentClass "bannercategory_content"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}