{assign 'productsListElement' $element->getProductsListElement()}

<script>
    window.productSearchForms = window.productSearchForms ? window.productSearchForms : [];
    window.productSearchForms.push({$element->getJsonInfo('list')});

{*    {if $element->canActLikeFilter()}*}
{*    window.productsListElementUrl = '{$productsListElement->URL}';*}
{*    {else}*}
{*    window.productsListElementUrl = '{$productsListElement->URL}id:{$productsListElement->id}/action:search/';*}
{*    {/if}*}
{*    {if $catalogueElement = $element->getProductCatalogue()}*}
{*    window.productSearchCatalogueUrl = '{$catalogueElement->URL}';*}
{*    {/if}*}
{*    window.categoriesUrls = window.categoriesUrls || {ldelim}{rdelim};*}
{*    {if $currentElement->type == 'category'}*}
{*    window.categoriesUrls[{$currentElement->id}] = '{$currentElement->URL}';*}
{*    {/if}*}
</script>

{if $element->checkboxesForParameters}
    {include file=$theme->template('component.productsfilter.tpl') titleType="label" selectorType='checkbox'}
{else}
    {include file=$theme->template('component.productsfilter.tpl') titleType="option" selectorType='dropdown'}
{/if}
{if $element->sortingEnabled}
    {include file=$theme->template('component.productssorter.tpl') titleType="option"}
{/if}

<div class="productsearch_reset button"><span class="button_text">{translations name="productsearch.reset"}</span></div>