{if $element->hasActualStructureInfo()}
    {assign var='formNames' value=$rootElement->getFormNames()}
    <form action="{$element->getFormActionURL()}" class="content_list_form" method="post" enctype="multipart/form-data">
        <div class='controls_block form_controls'>
            {include file=$theme->template("block.newelement.tpl") allowedTypes=$element->getAllowedTypes()}
            {if $element->getChildrenList()}

            <input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
            <input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />
            {if isset($rootPrivileges.deleteElements)}

                <button type='submit' onclick='if (!confirm("{translations name='message.deleteselectedconfirm'}")) return false;'
                        class='button warning_button important'><span class="icon icon_delete"></span>{translations name='button.deleteselected'}</button>
            {/if}
        </div>
        <table class='content_list'>
            <thead>
            <tr>
                <th class='checkbox_column'>
                    <input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
                </th>
                <th>
                    {translations name='label.name'}
                </th>
                <th class='edit_column'>

                </th>
                <th class='type_column'>
                    {translations name='label.type'}
                </th>
                <th class='date_column'>
                    {translations name='label.date'}
                </th>
                <th class='delete_column'>

                </th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$currentElement->getChildrenList() item=contentItem}
                {if $contentItem->structureType != 'positions'}
                    {assign var='typeName' value=$contentItem->structureType}
                    {assign var='typeLowered' value=$contentItem->structureType|strtolower}
                    {assign var='type' value="element."|cat:$typeLowered}
                    {assign var='privilege' value=$privileges.$typeName}
                    <tr class="content_list_item elementid_{$contentItem->id}">
                        <td class="checkbox_column">
                            <input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$contentItem->id}]" value="1" />
                        </td>
                        <td class='name_column'>
                            <span class='icon icon_productParameters'></span>
                            <a href="{$contentItem->URL}">
                                {$contentItem->getTitle()}
                            </a>
                        </td>
                        <td class="edit_column">
                            {if $privilege.showForm}
                                <a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
                            {/if}
                        </td>
                        <td class='type_column'>
                            {translations name=$type}
                        </td>
                        <td>
                            {$contentItem->dateModified}
                        </td>
                        <td class="delete_column">
                            {if $privilege.delete}
                                <a onclick='if (!confirm("{translations name='message.deleteconfirm1'} \"{$contentItem->getTitle()}\" {translations name='message.deleteconfirm2'}")) return false;' href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete'></a>
                            {/if}
                        </td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>

        {/if}
    </form>
{/if}
</div>