<div class="shoppingbasket_products {if !empty($checkout)} shoppingbasket_products_checkout{/if}"></div>
<script>
    window.templates = window.templates || {ldelim}{rdelim};
    window.templates['shoppingBasketStepProducts.internal.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.internal.tpl', true)};
    window.templates['shoppingBasketStepProducts.product.tpl'] = {$theme->getTemplateSource('shoppingBasketStepProducts.product.tpl', true)};
</script>