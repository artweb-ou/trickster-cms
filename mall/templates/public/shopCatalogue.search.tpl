{*{include file="component.breadcrumbs.tpl"}*}
<div class='category_details'>
	<div class='category_details_shops'>
	{foreach from=$element->foundElements item=shop}
		{include file=$theme->template("shop.short.tpl") element=$shop}
	{/foreach}
	</div>
</div>