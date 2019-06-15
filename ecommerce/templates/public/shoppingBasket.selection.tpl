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
            {assign var='formData' value=$element->getFormData()}
            {assign var='formErrors' value=$element->getFormErrors()}
            {assign var='formNames' value=$element->getFormNames()}

            <div class="shoppingbasket_step_container">
                {if $currentStep = $element->getCurrentStepElement()}
                    {include file=$theme->template($currentStep->getTemplate($currentStep->getCurrentLayout())) element=$currentStep shoppingBasketElement=$element}
                {/if}
            </div>
        </form>
    </div>
{/capture}

{assign moduleClass "shopping_basket_selection_block"}
{assign moduleTitleClass "shopping_basket_heading heading_1"}
{assign moduleTitleTag "div"}
{include file=$theme->template("component.contentmodule.tpl")}