{assign moduleTitle $element->title}
{capture assign="moduleSideContent"}
	{if $element->originalName != ""}
		<div class="category_thumbnail_image_container">
			<a href='{$element->URL}'>{include file=$theme->template('component.elementimage.tpl') type='categoryThumbnail' class='category_thumbnail_image' lazy=true}</a>
		</div>
	{/if}
{/capture}
{capture assign="moduleContent"}
	{stripdomspaces}
		{assign "subcategories" $element->getCategoriesList()}
		{if $subcategories}
			<ul class='category_with_subcategorylist_list'>
				{foreach $subcategories as $subcategory}
					{if !$subcategory->hidden}
						<li class="category_with_subcategorylist_item">
							<a class="subcategory_link" href='{$subcategory->URL}'>{$subcategory->title}</a>
						</li>
					{/if}
				{/foreach}
			</ul>
		{/if}
	{/stripdomspaces}
{/capture}
{assign moduleClass "category_with_subcategorylist"}
{assign moduleTitleClass "category_with_subcategorylist_title"}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_list.tpl")}