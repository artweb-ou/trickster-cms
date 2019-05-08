{if $h1 = $element->getH1()}
    {capture assign="moduleTitle"}
        {$h1}
    {/capture}
{elseif $element->title}
    {capture assign='moduleTitle'}
        {$element->title}
    {/capture}
{/if}
{capture assign="moduleContent"}
    {if $element->content}
        <div class="discountslist_content html_content">
            {$element->content}
        </div>
    {/if}
    <div class="discountslist_discounts">
        {foreach from=$element->getDiscounts() item=discount}
            {include file=$theme->template($discount->getTemplate()) element=$discount}
        {/foreach}
    </div>
{/capture}
{assign moduleClass "discountslist_block"}
{assign moduleTitleClass "discountslist_heading"}
{include file=$theme->template("component.contentmodule.tpl")}