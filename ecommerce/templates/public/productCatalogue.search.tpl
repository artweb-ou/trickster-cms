{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{assign 'products' $element->getProductsList()}
	{if $products}
		{assign 'pager' $element->getProductsPager()}
		{include file=$theme->template('pager.tpl') pager=$pager}
		<div class="productcatalogue_products productslist_products">
			{foreach $products as $product}
				{include file=$theme->template("product.{$element->getCurrentLayout('productsLayout')}.tpl") element=$product}
			{/foreach}
		</div>
		{include file=$theme->template('pager.tpl') pager=$pager}
	{else}
		{translations name="productcatalogue.no_products_found"}
	{/if}
{/capture}

{assign moduleClass "productcatalogue productcatalogue_search"}
{assign moduleContentClass "productcatalogue_content"}

{include file=$theme->template("component.contentmodule.tpl")}

