{if !isset($checkout)}{$checkout = false}{/if}
<div class="shoppingbasket_products"></div>
<script>
    window.templates = window.templates || {ldelim}{rdelim};
    window.templates['shoppingBasketStepProducts.internal.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.internal.tpl', true)};
    window.templates['shoppingBasketStepProducts.product.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.product.tpl', true)};
</script>