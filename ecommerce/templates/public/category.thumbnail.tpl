{assign moduleTitle $element->title}
{capture assign="moduleContent"}
	<div class="category_thumbnail_image_container">
		{if $element->originalName != ""}
			{include file=$theme->template('component.elementimage.tpl') type='categoryThumbnail' class='category_thumbnail_image' lazy=true}
		{/if}
	</div>

	<a class="category_thumbnail_button button category_short_link" href='{$element->URL}'>
		<span class='button_text'>{translations name='categories.viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "category_thumbnail_block category_short"}
{assign moduleTitleClass "category_thumbnail_title"}
{include file=$theme->template("component.subcontentmodule_square.tpl")}