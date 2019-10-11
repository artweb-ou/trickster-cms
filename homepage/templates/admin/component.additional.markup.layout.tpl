{if !empty($item.layouts)}
    <div class="form_table_layout form_table_layout_{$fieldName}">
        <div class="layout_header">
            <h1>
                {if isset($headingTitle)}
                    {$headingTitle}
                {else}
                    {translations name="layout.{$fieldName}"}
                {/if}
            </h1>
        </div>
        <div class="layout_content">
            {foreach $item.layouts as $fieldName=>$layout}
                <div class="layout_item{if !empty($layout.blockClass)} {$layout.blockClass}{/if}">
                    {include file=$theme->template("component.{$layout.type}.tpl") fieldName=$fieldName item=$layout}
                </div>
            {/foreach}
        </div>
    </div>
{/if}