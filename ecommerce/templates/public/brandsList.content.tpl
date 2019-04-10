{if $h1 = $element->getH1()}
	{assign moduleTitle $h1}
{elseif $element->title}
	{assign moduleTitle $element->title}
{/if}
{capture assign="moduleContent"}
	{if $element->content}
		<div class="brandslist_content html_content">
			{$element->content}
		</div>
	{/if}
	<div class="brandslist_grid">
		{foreach $element->getBrandsList() as $brand}
			{include file=$theme->template("brand.short.tpl") element=$brand}
		{/foreach}
	</div>
{/capture}

{assign moduleClass "brandslist_block"}
{assign moduleTitleClass "brandslist_heading"}
{include file=$theme->template("component.contentmodule.tpl")}