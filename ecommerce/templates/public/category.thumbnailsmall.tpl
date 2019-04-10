{assign moduleTitle $element->title}
{capture assign="moduleContent"}
	<div class="category_thumbnailsmall_image_container">
		{if $element->originalName != ""}
			{include file=$theme->template('component.elementimage.tpl') type='categoryThumbnailSmall' class='category_thumbnailsmall_image' lazy=true}
		{/if}
	</div>

	<a class="category_thumbnailsmall_button button category_short_link" href='{$element->URL}'>
		<span class='button_text'>{translations name='categories.viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "category_thumbnailsmall_block category_short"}
{assign moduleTitleClass "category_thumbnailsmall_title"}
{include file=$theme->template("component.subcontentmodule_square.tpl")}