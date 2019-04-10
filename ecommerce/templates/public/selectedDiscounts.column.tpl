{if $element->getDiscountsToDisplay()}
	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	{capture assign="moduleContent"}
		{foreach $element->getDiscountsToDisplay() as $discount}
			{include file=$theme->template("discount.column.tpl") element=$discount}
		{/foreach}
	{/capture}

	{assign moduleClass "selecteddiscounts_column"}
	{assign moduleContentClass "selecteddiscounts_column_content"}
	{assign moduleTitleClass "selecteddiscounts_column_title"}

	{include file=$theme->template("component.columnmodule.tpl")}
{/if}