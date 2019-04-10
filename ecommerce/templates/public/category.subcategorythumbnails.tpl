{assign moduleTitle $element->title}
{capture assign="moduleSideContent"}{/capture}
{capture assign="moduleContent"}
{stripdomspaces}
	<span class='category_with_subcategories_title_inroduction html_content'>
		{$element->introduction}
	</span>

	{assign "subcategories" $element->getCategoriesList()}
	{if $subcategories}
		<div class='category_with_subcategories_subcategories'>
			{foreach $subcategories as $subcategory}
				{if $subcategory->getLayout() != "hide"}
					{include file=$theme->template("category.thumbnail.tpl") element=$subcategory}
				{/if}
			{/foreach}
		</div>
	{/if}
{/stripdomspaces}
{/capture}


{assign moduleClass "category_with_subcategories"}
{assign moduleTitleClass "category_with_subcategories_title"}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}