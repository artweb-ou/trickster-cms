{assign var='formNames' value=$element->getFormNames()}
{assign var='formErrors' value=$element->getFormErrors()}
{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{if isset($shoppingBasket)}
		<a href='{$shoppingBasket->URL}' class='shoppingbasket_status'>
			<span class='shoppingbasket_status_empty'>{translations name='shoppingbasket.status_empty'}</span>
			<span class='shoppingbasket_status_amount'>{translations name='shoppingbasket.status_amount'}</span>
			<span class='shoppingbasket_status_price'>
				{translations name='shoppingbasket.status_totalsum'}: <span class='shoppingbasket_status_price_value'>{$shoppingBasket->totalPrice}</span>&#xa0;{$selectedCurrencyItem->symbol}
			</span>
		</a>
	{/if}
{/capture}

{assign moduleClass "shoppingbasketstatus_column"}
{assign moduleTitleClass "shoppingbasketstatus_column_title"}
{assign moduleContentClass "shoppingbasketstatus_column_content"}

{include file=$theme->template("component.columnmodule.tpl")}