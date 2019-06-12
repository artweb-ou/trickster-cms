{if isset($pager)}
	{include file=$theme->template("pager.tpl") pager=$pager}
{/if}
<div class="content_list_block">
	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">

		<div class='controls_block content_list_controls'>
			<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
			<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

			{include file=$theme->template("block.newelement.tpl") allowedTypes=$currentElement->getAllowedTypes()}
			{if count($currentElement->getChildCategories()) && isset($rootPrivileges.deleteElements)}
				<button type='submit' onclick='if (!confirm("{translations name='message.deleteselectedconfirm'}")) return false;'
						class='button warning_button'><span class="icon icon_delete"></span>{translations name='button.deleteselected'}</button>
			{/if}
			{if isset($rootPrivileges.moveElements)}
				<button class="button actions_form_move">{translations name="button.moveselected"}</button>
			{/if}
		</div>

		{assign var='formNames' value=$rootElement->getFormNames()}
		{if count($currentElement->getChildCategories())}
			<table class='content_list'>
				<thead>
					<tr>
						<th class='checkbox_column'>
							<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
						</th>
						<th class="name_column">
							{translations name='label.name'}
						</th>
						<th class='edit_column'>
							{translations name='label.edit'}
						</th>
						<th class='add_column'>
							{translations name='label.addsubcategory'}
						</th>
						<th class='type_column'>
							{translations name='label.type'}
						</th>
						<th class='date_column'>
							{translations name='label.date'}
						</th>
						<th class='delete_column'>
							{translations name='label.delete'}
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$currentElement->getChildCategories() item=contentItem}
						{if $contentItem->structureType != 'positions'}
							{assign var='typeName' value=$contentItem->structureType}
							{assign var='typeLowered' value=$contentItem->structureType|strtolower}
							{assign var='type' value="element."|cat:$typeLowered}
							{assign var='privilege' value=$privileges.$typeName}
							<tr class="content_list_item elementid_{$contentItem->id}">
								<td class="checkbox_cell">
									<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$contentItem->id}]" value="1" />
								</td>
								<td class='name_column'>
									{assign var='level' value=$contentItem->level-3}
									<a href="{$contentItem->URL}" style='padding-left: {$level*20}px' >
										<span class='icon icon_{$contentItem->structureType}'></span>{$contentItem->getTitle()}
									</a>
								</td>
								<td class="edit_column">
									{if $privilege.showForm}
										<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
									{/if}
								</td>
								<td>
									<a href="{$contentItem->URL}type:category/action:showForm/" class='icon category'></a>
								</td>
								<td class='type_column'>
						{translations name=$type}
								</td>
								<td>
									{$contentItem->dateModified}
								</td>
								<td class="delete_column">
									{if $privilege.delete}
										<a href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
									{/if}
								</td>
							</tr>
						{/if}
					{/foreach}
				</tbody>
			</table>
			<div class="content_list_bottom">
				{if isset($pager)}
					{include file=$theme->template("pager.tpl") pager=$pager}
				{/if}
			</div>
		{/if}
	</form>
</div>