{assign 'products' $element->getProductsList()}
{assign 'pager' $element->getProductsPager()}
{if $products}
	{capture assign="moduleContent"}
		{stripdomspaces}

		{if $element->title}
			{capture assign="moduleTitle"}
				{$element->title}
			{/capture}
		{/if}

		{if $element->content}
			<div class='selectedproducts_content html_content'>
				{$element->content}
			</div>
		{/if}

		{include file=$theme->template('component.productsfilter.tpl')}
		{if $element->isSortable() || ($element->getCurrentLayout() != 'scrolling' && $pager && count($pager->pagesList) > 1)}
			<div class="products_top_pager">
				{if $element->isSortable()}
					{include file=$theme->template('component.productssorter.tpl')}
				{/if}
				{include file=$theme->template('component.productslimit.tpl')}
				{if $element->getCurrentLayout() != 'scrolling'}
					{include file=$theme->template('pager.tpl')}
				{/if}
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
			{if $element->getCurrentLayout('productsLayout') == 'table'}
				{assign "parameters" $element->getUsedParametersInfo()}
				{assign "columns" $element->getActiveTableColumns()}
				<table class="selected_products_table table_component">
					<thead>
					{if $columns.title}<th>{translations name='category.title'}</th>{/if}
					{if $columns.code}<th>{translations name='product.code'}</th>{/if}
					{if $columns.unit}<th>{translations name='product.unit'}</th>{/if}
					{if $columns.minimumOrder}<th>{translations name='product.minimum'}</th>{/if}
					{if $columns.availability}<th>{translations name='product.stock'}</th>{/if}
					{foreach $parameters as $parameterInfo}
						<th>{$parameterInfo.title}</th>
					{/foreach}
					{if $columns.price}<th>{translations name='product.price'}</th>{/if}
					{if $columns.discount}<th>{translations name='product.discount'}</th>{/if}
					{if $columns.quantity && $shoppingBasket}<th>{translations name='product.quantity'}</th>{/if}
					{if $columns.basket && $shoppingBasket}
						<th></th>
					{/if}
					{if $columns.view}
						<th class="category_products_table_button_cell"></th>
					{/if}
					</thead>
					<tbody>
					{foreach $element->getProductsList() as $product}
						{include file=$theme->template('product.table.tpl') element=$product parameters=$parameters}
					{/foreach}
					</tbody>
				</table>
			{else}
				<div class="selected_products_container products_list">
					{foreach $products as $product}
						{include file=$theme->template($product->getTemplate($element->getCurrentLayout('productsLayout'))) element=$product selectedProductsElement=$element}
					{/foreach}
				</div>
			{/if}
		{/if}

	{if $element->getCurrentLayout() != 'scrolling'}
		{include file=$theme->template('pager.tpl') pager=$pager}
	{/if}
		{/stripdomspaces}
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
