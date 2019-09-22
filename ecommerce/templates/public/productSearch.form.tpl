{assign 'productsListElement' $element->getProductsListElement()}
{if $productsListElement}
    {if $element->title}
        {capture assign="moduleTitle"}
            {$element->title}
        {/capture}
    {/if}
    {capture assign="moduleContent"}
        {include file=$theme->template('productSearch.content.tpl')}
    {/capture}
    {assign moduleTitleClass "productsearch_title"}
    {assign moduleClass "productsearch"}
    {assign moduleAttributes "data-id='{$element->id}'"}
    {assign moduleContentClass "productsearch_content productsearch_content"}
    {include file=$theme->template("component.contentmodule.tpl")}
{/if}