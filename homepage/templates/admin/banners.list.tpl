{assign var='formNames' value=$rootElement->getFormNames()}
<div class="content_list_block">
	{if isset($pager)}
		{include file=$theme->template("pager.tpl") pager=$pager}
	{/if}

    <form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
        <div class='controls_block content_list_controls'>
            <input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id"/>
            <input type="hidden" class="content_list_form_action" value="deleteElements" name="action"/>

            {include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedTypes()}
        </div>

	{if $currentElement->getChildrenList()}
        <table class='content_list'>
            <thead>
                <tr>
                    <th class='checkbox_column'>
                        <input class='groupbox checkbox_placeholder' type="checkbox" value='1'/>
                    </th>
                    <th class='image_column'>
                        {translations name='label.image'}
                    </th>
                    <th class="name_column">
						{translations name='label.name'}
                    </th>
                    {*<th class=''></th>*}
                    <th class='edit_column'>
						{translations name='label.edit'}
                    </th>
                    <th>
						{translations name='banners.views'}
                    </th>
                    <th>
						{translations name='banners.clicks'}
                    </th>
                    <th class='date_column'>
						{translations name='label.dateCreated'}
                    </th>
                    <th class='date_column'>
						{translations name='label.dateModified'}
                    </th>
                    <th class='delete_column'>
						{translations name='label.delete'}
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
                            <td class="checkbox_cell">
                                <input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$contentItem->id}]" value="1"/>
                            </td>
                            <td>
                                {if $contentItem->originalName}
                                    {if $contentItem->type == 'image'}
                                        <img src='{$contentItem->getImageUrl()}' alt=" "/>
                                    {else}
                                        <div id="banner_{$contentItem->id}"></div>
                                        <script>
                                            /*<![CDATA[*/
                                            swfobject.embedSWF("{$controller->baseURL}file/mode:view/id:{$contentItem->image}/filename:{$contentItem->originalName}", "banner_{$contentItem->id}", '{$contentItem->width}', '{$contentItem->height}', '9.0.0');
                                            /*]]>*/
                                        </script>
                                    {/if}
                                {/if}
                            </td>
                            <td class='name_column'>
                                <a href="{$contentItem->URL}">
									{stripdomspaces}
                                        <span class='icon icon_{$contentItem->structureType}'></span>
							<span class="content_item_title">
									{$contentItem->getTitle()}
                            </span>
									{/stripdomspaces}
                                </a>
                            </td>
                            <td class='edit_column'>
								{if $privilege.showForm}
                                    <a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
								{/if}
                            </td>
                            <td class='view_column'>
								{$contentItem->views}
                            </td>
                            <td class='date_column'>
								{$contentItem->clicks}
                            </td>
                            <td class='date_column'>
								{$contentItem->dateCreated}
                            </td>
                            <td class='date_column'>
								{$contentItem->dateModified}
                            </td>
                            <td>
								{if $privilege.delete}
                                    <a href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
								{/if}
                            </td>
                        </tr>
					{/if}
				{/foreach}
            </tbody>
        </table>
	{/if}
    </form>
{if $currentElement->getChildrenList()}
    <div class="content_list_bottom">
	    {if isset($pager)}
	  		{include file=$theme->template("pager.tpl") pager=$pager}
	  	{/if}
    </div>
{/if}
</div>

