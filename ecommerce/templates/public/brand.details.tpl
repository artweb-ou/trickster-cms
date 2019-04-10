{if $element->title}
	{assign moduleTitle $element->title}
{/if}

{capture assign="moduleContent"}
	<div class='brand_details_top'>
		{if $element->originalName != ''}
			<div class="brand_details_image_block">
				{include file=$theme->template('component.elementimage.tpl') type='brandDetails' class='brand_details_image'}
			</div>
		{/if}

		{if $element->content != ''}
			<div class='brand_details_content html_content'>
				{$element->content}
			</div>
		{/if}

		{if $element->link != ''}
			<a class="brand_details_link newwindow_link" href="{$element->link}" title="{$element->title}">{$element->link}</a>
		{/if}
	</div>
	{stripdomspaces}
	{if count($element->getProductsList())}
		{if $element->isFilterable()}
			{include file=$theme->template('component.productsfilter.tpl')}
		{/if}
		<div class='brand_details_products'>
			<div class="products_top_pager">
				{if $element->isSortable()}
					{include file=$theme->template('component.productssorter.tpl')}
				{/if}
				{include file=$theme->template('component.productslimit.tpl')}
				{include file=$theme->template('pager.tpl') pager=$element->getProductsPager()}
			</div>
			{$template = $theme->template("product.{$element->getProductsLayout()}.tpl", true)}
			{if !$template}
				{$template = $theme->template('product.thumbnailsmall.tpl')}
			{/if}
			<div class="products_list">
				{foreach $element->getProductsList() as $product}
					{include file=$template element=$product}
				{/foreach}
			</div>
			{include file=$theme->template('pager.tpl') pager=$element->getProductsPager()}
		</div>
	{/if}
	{/stripdomspaces}
{/capture}

{assign moduleClass "brand_details"}
{include file=$theme->template("component.contentmodule.tpl")}