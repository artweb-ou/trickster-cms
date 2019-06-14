{if $element->errorMessage != ""}
	<p class="shoppingbasket_selection_error form_error" role="alert">{$element->errorMessage}</p>
{/if}
{foreach $element->getStepElements() as $stepElement}
	{include file=$theme->template($stepElement->getTemplate($stepElement->getCurrentLayout())) element=$stepElement shoppingBasketElement=$shoppingBasketElement}
{/foreach}
{include file=$theme->template('shoppingBasket.controls.tpl') element=$element}