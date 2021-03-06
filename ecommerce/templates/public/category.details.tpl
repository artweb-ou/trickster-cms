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
	{include file=$theme->template('category.subcategories.tpl')}
	{include file=$theme->template('component.productslist.tpl') layout=$element->getProductsLayout() productsListClass='category_details_products'}
	{/stripdomspaces}
{/capture}

{assign moduleClass "category_details_block"}
{assign moduleContentClass "category_details"}

{include file=$theme->template("component.contentmodule.tpl")}