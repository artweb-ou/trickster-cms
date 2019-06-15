{if $element->getLayout() != "hide" && $element->getCategoriesList() && !$element->isFiltrationApplied()}
    <div class='category_details_categories'>
        {foreach from=$element->getCategoriesList() item=subCategory}
            {include file=$theme->template($subCategory->getTemplate($element->getLayout())) element=$subCategory}
        {/foreach}
    </div>
{/if}
