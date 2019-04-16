{if $element->title}
	{capture assign="moduleTitle"}
		{if $element->productDiscount}
			<div class="discount_details_amount">-{$element->productDiscount}</div>{/if}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<div class='discount_details_top'>
		{if $element->originalName != ''}
			<div class="discount_details_image">
				<img src='{$controller->baseURL}image/type:discountDetails/id:{$element->image}/filename:{$element->originalName}' alt='{$element->title}'/>
			</div>
		{/if}
		<div class='discount_details_right'>
			{if $element->endDate || $element->conditionPrice}
				<div class='discount_details_info html_content'>
					{if $element->endDate}
						<div class='discount_details_dates'>
							{if $element->startDate}{translations name="discount.startdate"}: {$element->startDate} - {/if}{translations name="discount.enddate"}: {$element->endDate}
						</div>
					{/if}
					{if $element->conditionPrice}
						<div class='discount_details_condition'>
							{translations name="discount.condition"}: {$element->conditionPrice} {$selectedCurrencyItem->symbol}
						</div>
					{/if}
				</div>
			{/if}
			<div class='discount_details_content html_content'>
				{$element->content}
			</div>
		</div>
	</div>
	{if !$element->targetAllProducts}
		{stripdomspaces}
		{assign var="discountProducts" value=$element->getProductsList()}
		{if count($discountProducts)}
			<div class='discount_details_products'>
				{include file=$theme->template('component.productsfilter.tpl')}
				<div class="products_top_pager">
					{if $element->isSortable()}
						{include file=$theme->template('component.productssorter.tpl')}
					{/if}
					{include file=$theme->template('component.productslimit.tpl')}
					{include file=$theme->template('pager.tpl') pager=$element->getProductsPager()}
				</div>
				<div class="products_list">
					{foreach from=$discountProducts item=product}
						{include file=$theme->template("product.$productsLayout.tpl") element=$product}
					{/foreach}
				</div>

				{include file=$theme->template('pager.tpl') pager=$element->getProductsPager()}
			</div>
		{else}
			{translations name='discount.noproducts'}
		{/if}
		{/stripdomspaces}
	{/if}
{/capture}

{assign moduleTitleClass "discount_details_title"}
{assign moduleClass "discount_details"}
{include file=$theme->template("component.contentmodule.tpl")}