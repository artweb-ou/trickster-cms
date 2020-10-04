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
	{include file=$theme->template('component.productslist.tpl') layout=$element->getProductsLayout() componentClass="brand_details_products"}
{/capture}

{assign moduleClass "brand_details"}
{include file=$theme->template("component.contentmodule.tpl")}