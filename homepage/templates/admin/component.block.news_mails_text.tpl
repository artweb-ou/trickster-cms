{if $element->hasActualStructureInfo()}
    <div class="content_list_block">
        {if isset($pager)}
            {include file=$theme->template("pager.tpl") pager=$pager}
        {/if}
        {if $currentElement->getAllowedChildStructureTypes()}
            <form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
                <div class='controls_block content_list_controls'>
                    <input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
                    <input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

                    {include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedChildStructureTypes()}
                </div>
                {include file=$theme->template('shared.contentTable.tpl') contentList=$element->getSubContentElements()}
            </form>
        {/if}

        {if isset($pager) && $currentElement->getChildrenList()}
            {include file=$theme->template("pager.tpl") pager=$pager}
        {/if}
    </div>

    <div class="content_list_block">
        <form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
            <div class='controls_block content_list_controls'>
                <input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
                <input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

                {include file=$theme->template('block.buttons.tpl') allowedTypes=['newsMailSubContentCategory'] buttonId="bottom"}
            </div>
            {include file=$theme->template('shared.contentTable.tpl') contentList=$element->getSubCategories()}
        </form>
    </div>

    <div class="newsmails_history_block filtration_component">
        {assign var='formData' value=$element->getFormData()}
        {assign var='formErrors' value=$element->getFormErrors()}
        {assign var='formNames' value=$element->getFormNames()}
        <form action="{$element->getFormActionURL()}" class="form_component newsmailstext_form panel_component" method="post" enctype="multipart/form-data">
            <div class='newsmails_history panel_content'>
                <div class="newsmails_history_mails">
                    <div class="form_fields">
                        <div class="form_items">
                            <div class="form_label">
                                {translations name='newsmail.addresses'}
                            </div>
                            <div class="form_field">
                                <select class="select_multiple newsmailtext_address_select" multiple='multiple' name="{$formNames.selectedEmails}[]" autocomplete='off'>
                                </select>
                            </div>
                        </div>
                        <div class="form_items">
                            <div class="form_label">
                                {translations name='newsmail.groups'}
                            </div>
                            <div class="form_field">
                                <input class="checkbox_placeholder newsmailstext_group_checkbox" type="checkbox" value="1" name="newsmailstext_group_select" /> {translations name='newsmail.select_all'}
                                {foreach from=$element->result item=group}
                                    <div class="newsmails_history_group_row">
                                        <input class="newsmailstext_group groupelement_{$group->id} checkbox_placeholder" type="checkbox" value="{$group->id}" name="{$formNames.selectedGroupsIds}[]" /> {$group->title}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>

                <input class="button" type="submit" value='{translations name='button.startsending'}'/>
                <input type="hidden" value="{$element->id}" name="id" />
                <input type="hidden" value="sendEmails" name="action" />
            </div>
        </form>
        <script type="text/javascript">
            /*<![CDATA[*/
            window.newsMailsGroups = {ldelim}{rdelim};
            {foreach from=$element->groupsList item=group}
            window.newsMailsGroups[{$group->id}] = {ldelim}{rdelim};
            {/foreach}
            /*]]>*/
        </script>
    </div>
{/if}