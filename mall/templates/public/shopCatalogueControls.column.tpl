{stripdomspaces}
{$catalogue = $element->getCatalogue()}
{if $element->title}
	{capture assign='moduleTitle'}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{$currentTab = 'categories'}
	{if $controller->getParameter('letter')}
		{$currentTab = 'abc'}
	{/if}
	<div class="shop_catalogue_controls_buttons">
		<a class="shop_catalogue_controls_button{if $currentTab == 'categories'} shop_catalogue_controls_button_active{/if}" href="{$catalogue->URL}">
			{translations name='shopcataloguecontrols.by_categories'}
		</a>
		<a class="shop_catalogue_controls_button{if $currentTab != 'categories'} shop_catalogue_controls_button_active{/if}" href="{$catalogue->URL}letter:all/">
			{translations name='shopcataloguecontrols.by_letters'}
		</a>
	</div>
	{if $currentTab == 'categories'}
		<div class="shop_catalogue_controls_options shop_catalogue_controls_categories">
			{$categories = $element->getCategories()}
			{foreach $categories as $category}
				<a class="shop_catalogue_controls_category" href="{$catalogue->URL}category:{$category->id}/">
					{$category->title}
				</a>
			{/foreach}
		</div>
	{else}
		<div class="shop_catalogue_controls_options shop_catalogue_controls_letters">
			{$letters = $element->getShopIndexLetters()}
			{foreach $letters as $letter}
				<a class="shop_catalogue_controls_letter" href="{$catalogue->URL}letter:{$letter}/"><!--
					---->{$letter}<!---
				----></a>
			{/foreach}
		</div>
	{/if}
{/capture}

{assign moduleClass "shop_catalogue_controls"}
{assign moduleTitleClass "shop_catalogue_controls_title"}
{assign moduleContentClass "shop_catalogue_controls_content"}
{include file=$theme->template("component.columnmodule.tpl")}
{/stripdomspaces}