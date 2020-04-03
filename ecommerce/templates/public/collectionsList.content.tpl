{if $h1 = $element->getH1()}
	{assign moduleTitle $h1}
{elseif $element->title}
	{assign moduleTitle $element->title}
{/if}
{capture assign="moduleContent"}
	{if $element->content}
		<div class="collectionslist_content html_content">
			{$element->content}
		</div>
	{/if}
	<div class="collectionslist_grid">
		{foreach $element->getCollectionsList() as $collection}
			{include file=$theme->template($collection->getTemplate($element->getCurrentLayout('collection'))) element = $collection }
		{/foreach}
	</div>
{/capture}

{assign moduleClass "collectionslist_block"}
{assign moduleTitleClass "collectionslist_heading"}
{include file=$theme->template("component.contentmodule.tpl")}