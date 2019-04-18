{assign moduleTitle $element->title}
{if $element->originalName != ''}
	{capture assign="moduleSideContent"}
		{include file=$theme->template('component.elementimage.tpl') element=$element type='discountShort' lazy=true}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<div class='discount_short_right'>
		{if $element->endDate || $element->conditionPrice}
			<div class='discount_short_info html_content'>
				{if $element->endDate}
					<div class='discount_short_dates'>
						{if $element->startDate}{translations name="discount.startdate"}: {$element->startDate} - {/if}{translations name="discount.enddate"}: {$element->endDate}
					</div>
				{/if}
				{if $element->conditionPrice}
					<div class='discount_short_condition'>
						{translations name="discount.condition"}: {$element->conditionPrice} {$selectedCurrencyItem->symbol}
					</div>
				{/if}
			</div>
		{/if}
		<div class='discount_short_content html_content'>
			{$element->content}
		</div>
	</div>
{/capture}

{capture assign="moduleControls"}
	<a class="discount_short_button button" href="{$element->URL}">
		<span class='button_text'>{translations name='discount.viewproducts'}</span>
	</a>
{/capture}

{assign moduleClass "discount_short"}
{assign moduleTitleClass "discount_short_title"}
{assign moduleAttributes ""}
{assign moduleSideContentClass "discount_short_image"}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}