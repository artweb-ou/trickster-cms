{if $element->title}
    {capture assign="moduleTitle"}
        {$element->title}
    {/capture}
{/if}
{capture assign="moduleContent"}
    {stripdomspaces}
    {if $personnelList = $element->getPersonnelList()}
        {if $element->getCurrentLayout() == 'table'}
            <table class="personnellist_table table_component">
                <thead>
                <tr>
                    <th>{translations name='personnel.position'}</th>
                    <th>{translations name='personnel.name'}</th>
                    <th>{translations name='personnel.phone'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$personnelList item=personnel}
                    <tr>
                        <td>
                            {$personnel->position}
                        </td>
                        <td>
                            {if $element->email != ''}<a href="mailto:{$element->email}">{/if}{$personnel->title}{if $element->email != ''}</a>{/if}
                        </td>
                        <td>
                            {if $personnel->phone}<a href="tel:{$personnel->phone}">{$personnel->phone}</a>{/if}
                            {if $personnel->mobilePhone}<a href="tel:{$personnel->mobilePhone}">{$personnel->mobilePhone}</a>{/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            <div class="personnellist_items">
                {foreach from=$personnelList item=personnel}
                    {include file=$theme->template($personnel->getTemplate($element->getCurrentLayout())) element=$personnel}
                {/foreach}
            </div>
        {/if}
    {/if}

    {/stripdomspaces}
{/capture}

{assign moduleClass "personnellist_block"}
{assign moduleTitleClass "personnellist_heading"}
{assign moduleContentClass "personnellist_content"}

{include file=$theme->template("component.contentmodule.tpl")}