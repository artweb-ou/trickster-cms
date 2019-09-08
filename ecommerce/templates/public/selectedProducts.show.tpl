{assign 'products' $element->getProductsList()}
{assign 'pager' $element->getProductsPager()}
{if $products}
	{capture assign="moduleContent"}
		{if !empty($element->getTitle())}
			{capture assign="moduleTitle"}
				{$element->getTitle()}
			{/capture}
		{/if}
		{if !empty($element->content)}
			<div class='selectedproducts_content html_content'>
				{$element->content}
			</div>
		{/if}
		{if $element->getCurrentLayout() == 'scrolling'}
			<div class="selectedproducts_scroll" data-auto="1">
				{foreach $products as $product}
					{include file=$theme->template($product->getTemplate($element->getCurrentLayout("productsLayout"))) element=$product selectedProductsElement=$element}
				{/foreach}
			</div>
			<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_left scroll_pages_previous"></div>
			<div class="selectedproducts_scrollbutton scroll_pages_button selectedproducts_scrollbutton_right scroll_pages_next"></div>
		{else}
			{include file=$theme->template('component.productslist.tpl') layout=$element->element->getCurrentLayout('productsLayout') componentClass="selected_products_container"}
		{/if}
		{if !empty($element->buttonTitle) && (!empty($element->buttonUrl) || !empty($element->getButtonConnectedMenuUrl()))}
			{if $element->getButtonConnectedMenuUrl()}
				{$Url = $element->getButtonConnectedMenuUrl()}
			{else}
				{$Url = $element->buttonUrl}
			{/if}
			<div class="view_all">
				<a href="{$Url}" class="button view_all_button"><span class="button_text">{$element->buttonTitle}</span></a>
			</div>
		{/if}
	{/capture}

	{assign moduleClass "selected_products_block"}
	{if $element->getCurrentLayout() != 'scrolling'}
		{assign moduleContentClass "selectedproducts_content"}
	{else}
		{assign moduleContentClass "selectedproducts_content selectedproducts_content_scrolltype"}
	{/if}
	{assign moduleTitleClass "selectedproducts_heading"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}