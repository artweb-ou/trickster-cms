{* TODO: Add controll for categories count, not to display if count 0 *}

{assign 'pager' $element->getProductsPager()}

{if $element->title && $element->productsLayout != "hide"}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{stripdomspaces}
	{if $element->originalName || $element->content}
		<div class="category_details_top_block">
			{if $element->originalName}
				<div class="category_details_image_wrap">
					{include file=$theme->template('component.elementimage.tpl') type='categoryDetails' class='category_details_image'}
				</div>
			{/if}

			{if $element->content}
				<div class='category_details_content html_content'>
					{$element->content}
				</div>
			{/if}
		</div>
	{/if}

	{include file=$theme->template('component.productslist.tpl') layout=$element->productsLayout componentClass="brand_details_products"}
	{/stripdomspaces}
{/capture}

{assign moduleClass "category_details_block"}
{assign moduleContentClass "category_details"}

{include file=$theme->template("component.contentmodule.tpl")}

