{capture assign="moduleContent"}
	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	<div class='shoppingbasket_selection'>
		<div class="shoppingbasket_selection_message">
			{$element->shoppingBasket->getMessage()}
		</div>
		<form action="{$element->getFormActionURL()}" class='shoppingbasket_contents shoppingbasket_form' method="post" enctype="multipart/form-data" role="form">
            {if $element->errorMessage != ""}
                <p class="shoppingbasket_selection_error form_error" role="alert">{$element->errorMessage}</p>
            {/if}
            {assign var='formData' value=$element->getFormData()}
            {assign var='formErrors' value=$element->getFormErrors()}
            {assign var='formNames' value=$element->getFormNames()}

			{include file=$theme->template('shoppingBasket.products.tpl') element=$element}
			{include file=$theme->template('shoppingBasket.discounts.tpl') element=$element}
			{include file=$theme->template('shoppingBasket.selection_form.tpl') element=$element}
			{include file=$theme->template('shoppingBasket.totals.tpl')}

			{if !$element->isCheckoutStepEnabled()}
				{include file=$theme->template('shoppingBasket.paymentmethods.tpl')}
			{/if}
			{include file=$theme->template('shoppingBasket.controls.tpl') element=$element}
		</form>
	</div>
{/capture}

{assign moduleClass "shopping_basket_selection_block"}
{assign moduleTitleClass "shopping_basket_heading"}
{include file=$theme->template("component.contentmodule.tpl")}