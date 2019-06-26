<div class="shoppingbasket_products"
     {if !empty($checkout)}data-checkout="{$checkout}"{/if}
     data-template-internal = 'shoppingBasketStepProducts.internal.tpl'
></div>
<script>
    window.templates = window.templates || {ldelim}{rdelim};
    window.templates['shoppingBasketStepProducts.internal.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.internal.tpl', true)};
    window.templates['shoppingBasketStepProducts.product.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.product.tpl', true)};
    window.templates['element.productAmountControlsBlock.tpl'] = {$theme->getTemplateSource('element.productAmountControlsBlock.tpl', true)};
</script>