{$productsList=$element->getProductsList()}
{$pager=$element->getProductsPager()}
{if $layout != "hide"}
    {$template = "product.{$layout}.tpl"}
    <div class="productslist_component{if !empty($componentClass)} {$componentClass}{/if}" data-id="{$element->id}">
        {include file=$theme->template('component.productsfilter.tpl') displayFilterTopInfo=true}
        {if $pager && count($pager->pagesList)>1 || $element->isSortable()}
            <div class="products_top_pager">
                {include file=$theme->template('pager.tpl') pager=$pager}
                <div class="products_top_pager_controls">
                    {include file=$theme->template('component.productslimit.tpl')}
                    {if $element->isSortable()}
                        {include file=$theme->template('component.productssorter.tpl')}
                    {/if}
                </div>
            </div>
        {/if}
        {* Products *}
        {if $layout != "hide"}
            {if $layout == 'table'}
                {assign "parameters" $element->getUsedParametersInfo()}
                {assign "columns" $element->getActiveTableColumns()}
                <table class="category_products_table table_component">
                    <thead>
                    {if $columns.title}
                        <th>{translations name='category.title'}</th>{/if}
                    {if $columns.code}
                        <th>{translations name='product.code'}</th>{/if}
                    {if $columns.unit}
                        <th>{translations name='product.unit'}</th>{/if}
                    {if $columns.minimumOrder}
                        <th>{translations name='product.minimum'}</th>{/if}
                    {if $columns.availability}
                        <th>{translations name='product.stock'}</th>{/if}
                    {foreach $parameters as $parameterInfo}
                        <th>{$parameterInfo.title}</th>
                    {/foreach}
                    {if $columns.price}
                        <th>{translations name='product.price'}</th>{/if}
                    {if $columns.discount}
                        <th>{translations name='product.discount'}</th>{/if}
                    {if $columns.quantity && $shoppingBasket}
                        <th>{translations name='product.quantity'}</th>{/if}
                    {if $columns.basket && $shoppingBasket}
                        <th></th>
                    {/if}
                    {if $columns.view}
                        <th class="category_products_table_button_cell"></th>
                    {/if}
                    </thead>
                    <tbody>
                    {foreach $element->getProductsList() as $product}
                        {include file=$theme->template('product.table.tpl') element=$product parameters=$parameters}
                    {/foreach}
                    </tbody>
                </table>
            {else}
                <div class='productslist_products {if !empty($productsListClass)} {$productsListClass}{/if}'>
                    {foreach $element->getProductsList() as $product}
                        {include file=$theme->template($template, true) element=$product}
                    {/foreach}
                </div>
            {/if}
        {/if}

        {include file=$theme->template('pager.tpl') pager=$pager}
    </div>

    <script>
        window.productsLists = window.productsLists ? window.productsLists : [];
        window.productsLists.push({$element->getJsonInfo('list')});

        window.templates = window.templates || {ldelim}{rdelim};
        window.templates['{$template}'] = {$theme->getTemplateSource($template, true)};
        window.templates['component.subcontentmodule_wide.tpl'] = {$theme->getTemplateSource('component.subcontentmodule_wide.tpl', true)};
        window.templates['component.subcontentmodule_square.tpl'] = {$theme->getTemplateSource('component.subcontentmodule_square.tpl', true)};
        window.templates['component.elementimage.tpl'] = {$theme->getTemplateSource('component.elementimage.tpl', true)};
        window.templates['component.filterdropdown.tpl'] = {$theme->getTemplateSource('component.filterdropdown.tpl', true)};
    </script>
{/if}

