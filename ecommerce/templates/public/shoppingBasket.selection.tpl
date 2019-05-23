{capture assign="moduleTitle"}
    {$element->getH1()}
{/capture}
{capture assign="moduleContent"}
    <div class='shoppingbasket_selection'>
        <div class="shoppingbasket_selection_message">
            {$element->shoppingBasket->getMessage()}
        </div>
        <form action="{$element->getFormActionURL()}" class='shoppingbasket_contents shoppingbasket_form' method="post"
              enctype="multipart/form-data" role="form">
            <div class="shoppingbasket_step_container">
                {if $element->errorMessage != ""}
                    <p class="shoppingbasket_selection_error form_error" role="alert">{$element->errorMessage}</p>
                {/if}
                {assign var='formData' value=$element->getFormData()}
                {assign var='formErrors' value=$element->getFormErrors()}
                {assign var='formNames' value=$element->getFormNames()}

                {if $element->getSteps()}
                    {foreach $element->getCurrentStepElements() as $contentItem}
                        {include file=$theme->template($contentItem->getTemplate()) element=$contentItem shoppingBasketElement=$element}
                    {/foreach}
                {else}
                    {*Default steps:*}
                    {*1:*}
                    {*products*}
                    {*discounts*}
                    {*delivery*}
                    {*totals*}
                    {*2:*}
                    {*paymentmethods*}
                    {*3:*}
                    {*checkoutTotals*}
                {/if}
            </div>
            {include file=$theme->template('shoppingBasket.controls.tpl') element=$element}
        </form>
    </div>
{/capture}

{assign moduleClass "shopping_basket_selection_block"}
{assign moduleTitleClass "shopping_basket_heading"}
{include file=$theme->template("component.contentmodule.tpl")}