{assign 'pager' $element->getProductsPager()}

{if $h1 = $element->getH1()}{assign moduleTitle $h1}{/if}
{capture assign="moduleContent"}
    {stripdomspaces}
    {if $element->originalName || $element->content}
		<div class="collection_details_top_block">
            {if $element->originalName}
				<div class="collection_details_image_wrap">
                    {include file=$theme->template('component.elementimage.tpl') type='collectionDetails' class='collection_details_image'}
				</div>
            {/if}

			<div class="collection_details_right">
                {if $element->content}
					<div class='collection_details_content html_content'>
                        {$element->content}
					</div>
                {/if}
			</div>
		</div>
    {/if}
        {include file=$theme->template('component.productsfilter.tpl') displayFilterTopInfo=true}
		{if $pager && count($pager->pagesList)>1 || $element->isSortable()}
			<div class="products_top_pager">
				{include file=$theme->template('pager.tpl') pager=$pager}
				<div class="products_top_pager_controls">
					{include file=$theme->template('component.productslimit.tpl')}
					{if $element->isSortable()}
						{include file=$theme->template('component.productssorter.tpl')}
					{/if}
				</div>
			</div>
		{/if}
		<div class='collection_details_products products_list'>

            {$template = $theme->template("product.{$element->getProductsLayout()}.tpl", true)}
            {if !$template}
                {$template = $theme->template('product.thumbnailsmall.tpl')}
            {/if}
            {foreach $element->getProductsList() as $product}
                {include file=$template element=$product}
            {/foreach}
		</div>
    {/stripdomspaces}
{/capture}

{assign moduleClass "collection_details_block"}
{assign moduleContentClass "collection_details"}

{include file=$theme->template("component.contentmodule.tpl")}

