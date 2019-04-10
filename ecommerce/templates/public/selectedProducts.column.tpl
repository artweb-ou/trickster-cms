{assign 'products' $element->getProductsList()}
{if $products}
	{capture assign="moduleContent"}
		{if $element->title}
			{capture assign="moduleTitle"}
				{$element->title}
			{/capture}
		{/if}

		<div class="selectedproducts_column_products">
			{foreach $products as $product}

				<a class="selectedproducts_column_product slide" href="{$product->URL}" title="{$product->title}">
					<span class="selectedproducts_column_product_image_wrap">
						{include file=$theme->template('component.elementimage.tpl') element=$product type='columnProduct' class='selectedproducts_column_product_image' lazy=true}
					</span>
				</a>

			{/foreach}
		</div>

		{if $element->content}
			<div class='selectedproducts_column_content html_content'>
				{$element->content}
			</div>
		{/if}
	{/capture}
{/if}

{assign moduleClass "selectedproducts_column"}
{assign moduleContentClass "selectedproducts_column"}
{assign moduleTitleClass 'selectedproducts_column_heading'}

{include file=$theme->template("component.columnmodule.tpl")}