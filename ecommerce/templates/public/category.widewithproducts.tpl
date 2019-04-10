{assign moduleTitle $element->title}
{capture assign="moduleContent"}
	{if $element->introduction}
		<div class='category_short_content html_content'>
			{$element->introduction}
		</div>
	{/if}
	<div class="category_short_products products_list">
		{foreach from=$element->getTopProductsList() item=product}
			{include file=$theme->template('product.thumbnailsmall.tpl') element=$product}
		{/foreach}
	</div>
{/capture}

{capture assign="moduleControls"}
	<a class="category_short_button button category_short_link" href='{$element->URL}'>
		<span class='button_text'>{translations name='categories.category_short_viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "category_withproducts category_short"}
{assign moduleTitleClass "category_short_title"}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}