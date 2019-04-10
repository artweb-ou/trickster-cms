{assign moduleTitle $element->title}
{if $element->originalName != ""}
	{capture assign="moduleSideContent"}
		{include file=$theme->template('component.elementimage.tpl') type='brandShort' class='brand_short_image' lazy=true}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<span class="brand_short_content html_content">
		{$element->introduction}
	</span>
{/capture}
{capture assign="moduleControls"}
	<a class="brand_short_button button" href="{$element->URL}">
		<span class="button_text">{translations name="brand.short_viewproducts"}</span>
	</a>
{/capture}

{assign moduleClass "brand_short"}
{assign moduleTitleClass "brand_short_title"}
{assign moduleSideContentClass "brand_short_image_block"}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}