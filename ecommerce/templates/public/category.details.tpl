{assign 'pager' $element->getProductsPager()}

{if $h1 = $element->getH1()}{assign moduleTitle $h1}{/if}
{capture assign="moduleContent"}
	{stripdomspaces}
	{if $element->originalName || $element->content}
		<div class="category_details_top_block">
			{if $element->originalName}
				<div class="category_details_image_wrap">
					{include file=$theme->template('component.elementimage.tpl') type='categoryDetails' class='category_details_image'}
				</div>
			{/if}

			<div class="category_details_right">
				{if $element->content}
					<div class='category_details_content html_content'>
						{$element->content}
					</div>
				{/if}
			</div>
		</div>
	{/if}
	{include file=$theme->template('category.subcategories.tpl') element=$element}
	{if $element->getProductsLayout() != "hide"}
		<div class="category_details_products_related">
			{if $element->isFilterable()}
				{include file=$theme->template('component.productsfilter.tpl')}
			{/if}
			{if $pager && count($pager->pagesList)>1 || $element->isSortable()}
			<div class="products_top_pager">
				{include file=$theme->template('pager.tpl') pager=$pager}
				<div class="products_top_pager_controls">
					{if $element->isSortable()}
						{include file=$theme->template('component.productssorter.tpl')}
					{/if}
					{include file=$theme->template('component.productslimit.tpl')}
				</div>
			</div>
			{/if}
			{* Products *}
			{if $element->getProductsLayout() != "hide"}
				{if $element->getProductsLayout() == 'table'}
					{assign "parameters" $element->getUsedParametersInfo()}
					{assign "columns" $element->getActiveTableColumns()}
					<table class="category_products_table table_component">
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
					<div class='category_details_products products_list'>
						{$template = $theme->template("product.{$element->getProductsLayout()}.tpl", true)}
						{if !$template}
							{$template = $theme->template('product.thumbnailsmall.tpl')}
						{/if}
						{foreach $element->getProductsList() as $product}
							{include file=$template element=$product}
						{/foreach}
					</div>
				{/if}
			{/if}

			{include file=$theme->template('pager.tpl') pager=$pager}
		</div>
	{/if}
	{/stripdomspaces}
{/capture}

{assign moduleClass "category_details_block"}
{assign moduleContentClass "category_details"}

{include file=$theme->template("component.contentmodule.tpl")}

