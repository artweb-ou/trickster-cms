{assign moduleTitle $element->title}
{if $element->originalName != ""}
	{$moduleSideContent = ''}
{/if}
{capture assign="moduleContent"}
	<div class="collection_detailed_content html_content">
		{$element->introduction}
	</div>
	<div>
		{foreach $element->getConnectedProducts() as $product}
            {include file=$theme->template($product->getTemplate($element->getProductsLayout())) element = $product}
		{/foreach}
	</div>
{/capture}
{capture assign="moduleControls"}
	<a class="collection_detailed_button button" href="{$element->URL}">
		<span class="button_text">{translations name="brand.short_viewproducts"}</span>
	</a>
{/capture}

{assign moduleClass "collection_detailed"}
{assign moduleTitleClass "collection_detailed_title"}
{assign moduleSideContentClass ""}
{assign moduleAttributes ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}