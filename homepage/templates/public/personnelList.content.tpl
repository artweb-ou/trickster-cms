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
                            {$personnel->title}
                        </td>
                        <td>
                            {if $personnel->phone}
                                <div>{$personnel->phone}</div>{/if}
                            {if $personnel->mobilePhone}
                                <div>{$personnel->mobilePhone}</div>{/if}
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