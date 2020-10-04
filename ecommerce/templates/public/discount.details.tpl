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
		{include file=$theme->template('component.productslist.tpl') layout=$productsLayout componentClass="discount_details_products"}
	{/if}
{/capture}

{assign moduleTitleClass "discount_details_title"}
{assign moduleClass "discount_details"}
{include file=$theme->template("component.contentmodule.tpl")}