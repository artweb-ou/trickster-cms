{assign moduleTitle $element->title}
{if $element->originalName != ''}
	{capture assign="moduleSideContent"}
		{include file=$theme->template('component.elementimage.tpl') type='categoryShort' class='category_short_image' lazy=true}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<span class='category_short_content html_content'>
		{$element->introduction}
	</span>
{/capture}

{capture assign="moduleControls"}
	<a class="category_short_button category_short_link button" href='{$element->URL}'>
		<span class='button_text'>{translations name='categories.category_short_viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "category_short_block category_short"}
{assign moduleTitleClass "category_short_title"}
{assign moduleAttributes ""}
{assign moduleSideContentClass "category_short_image_block"}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}