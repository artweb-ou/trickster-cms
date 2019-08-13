{assign moduleTitle $element->title}
{if $element->originalName != ""}
	{$moduleSideContent = ''}
{/if}
{capture assign="moduleContent"}
	<div class="collection_detailed_content html_content">
		{$element->introduction}
	</div>
	<div>
		<div class="selectedproducts_scroll" data-auto="1">
		{foreach $element->getConnectedProducts() as $product}
            {include file=$theme->template($product->getTemplate($element->getProductsLayout())) element = $product}
		{/foreach}
	</div>
	</div>
	<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_left scroll_pages_previous"></div>
	<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_right scroll_pages_next"></div>
{/capture}
{capture assign="moduleControls"}
	<a class="collection_detailed_button button" href="{$element->URL}">
		<span class="button_text">{translations name="brand.short_viewproducts"}</span>
	</a>
{/capture}

{assign moduleClass "collection_detailed"}
{assign moduleTitleClass "collection_detailed_title"}
{assign moduleSideContentClass ""}
{assign moduleContentClass "selectedproducts_content selectedproducts_content_scrolltype"}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}