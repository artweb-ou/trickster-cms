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

	{if $element->productsLayout != "hide" && $element->getProductsList()}

		{include file=$theme->template('component.productsfilter.tpl') displayFilterTopInfo=true}
		<div class="products_top_pager">
			{if $element->isSortable()}
				{include file=$theme->template('component.productssorter.tpl')}
			{/if}
			{include file=$theme->template('component.productslimit.tpl')}
			{include file=$theme->template('pager.tpl') pager=$pager}
		</div>
		{* Products *}
		{if $element->productsLayout != "hide"}
			{if $element->productsLayout == 'table'}
                {assign "parameters" $element->getUsedParametersInfo()}
				<table class="category_products_table table_component">
					<thead>
						<th>{translations name='category.title'}</th>
                        {foreach $parameters as $parameterInfo}
                                <th>{$parameterInfo.title}</th>
                        {/foreach}
						<th>{translations name='product.price'}</th>
						<th>{translations name='product.discount'}</th>
						<th></th>
						<th></th>
					</thead>
					<tbody>
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template('product.table.tpl') element=$product parameter=$parameters}
						{/foreach}
					</tbody>
				</table>
			{else}
				<div class='category_details_products productslist_products'>
					{if $element->productsLayout == "thumbnailsmall"}
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template('product.thumbnailsmall.tpl') element=$product}
						{/foreach}
					{elseif $element->productsLayout == "thumbnail"}
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template('product.thumbnail.tpl') element=$product}
						{/foreach}
					{elseif $element->productsLayout == "detailed"}
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template('product.detailed.tpl') element=$product}
						{/foreach}
					{elseif $element->productsLayout == "wide"}
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template('product.wide.tpl') element=$product}
						{/foreach}
					{else}
						{foreach $element->getProductsList() as $product}
							{include file=$theme->template("product.{$element->productsLayout}.tpl") element=$product}
						{/foreach}
					{/if}
				</div>
			{/if}
		{/if}

		{include file=$theme->template('pager.tpl') pager=$pager}
	{/if}
	{/stripdomspaces}
{/capture}

{assign moduleClass "category_details_block"}
{assign moduleContentClass "category_details"}

{include file=$theme->template("component.contentmodule.tpl")}

