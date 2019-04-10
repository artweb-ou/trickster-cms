{assign moduleTitle $discount->title}
{if $discount->originalName != ''}
	{capture assign="moduleSideContent"}
		{include file=$theme->template('component.elementimage.tpl') element=$discount type='discountShort' lazy=true}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<div class='discount_short_right'>
		{if $discount->endDate || $discount->conditionPrice}
			<div class='discount_short_info html_content'>
				{if $discount->endDate}
					<div class='discount_short_dates'>
						{if $discount->startDate}{translations name="discount.startdate"}: {$discount->startDate} - {/if}{translations name="discount.enddate"}: {$discount->endDate}
					</div>
				{/if}
				{if $discount->conditionPrice}
					<div class='discount_short_condition'>
						{translations name="discount.condition"}: {$discount->conditionPrice} {$selectedCurrencyItem->symbol}
					</div>
				{/if}
			</div>
		{/if}
		<div class='discount_short_content html_content'>
			{$discount->content}
		</div>
	</div>
{/capture}

{capture assign="moduleControls"}
	<a class="discount_short_button button" href="{$discount->URL}">
		<span class='button_text'>{translations name='discount.viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "discount_short"}
{assign moduleTitleClass "discount_short_title"}
{assign moduleAttributes ""}
{assign moduleSideContentClass "discount_short_image"}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}