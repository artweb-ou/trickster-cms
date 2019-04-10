<div class="content_list_block">
	{if isset($pager)}
		{include file=$theme->template("pager.tpl") pager=$pager}
	{/if}

	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">

		{if $currentElement->getAllowedChildStructureTypes()}
			<div class='controls_block content_list_controls'>
				<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
				<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

				{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedChildStructureTypes()}
			</div>
		{/if}

		{assign var='formNames' value=$rootElement->getFormNames()}
		{if $currentElement->getTemplateColumnsList()}
			{*  __ data table *}
			<table class='content_list'>
				<thead>
					<tr>
						<th class='checkbox_column'>
							<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
						</th>
						<th class="name_column">
							{translations name='productimporttemplate.name'}
						</th>
						<th class="">
							{translations name='productimporttemplate.productvariable'}
						</th>
						<th class="">
							{translations name='productimporttemplate.columnnumber'}
						</th>
						<th class="">
							{translations name='productimporttemplate.mandatory'}
						</th>
						<th class='edit_column'>
							{translations name='label.edit'}
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
				{foreach from=$currentElement->getTemplateColumnsList() item=contentItem}
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
							<a href="{$contentItem->URL}">
								{stripdomspaces}
								<span class='icon icon_{$contentItem->structureType}'></span>
								<span class="content_item_title">
									{$contentItem->getTitle()} ({$contentItem->productVariable})
								</span>
								{/stripdomspaces}
							</a>
						</td>
						<td>
							{$contentItem->productVariable}{if $contentItem->productVariable=='parameter'}: {$contentItem->getSelectedProductParameterTitle()}{/if}
						</td>
						<td>
							{$contentItem->columnNumber} {$contentItem->numToChar($contentItem->columnNumber)}
						</td>
						<td>
							{$contentItem->mandatory}
						</td>
						<td class='edit_column'>
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
	<div class="content_list_bottom">
		{if isset($pager) && $currentElement->getChildrenList()}
			{include file=$theme->template("pager.tpl") pager=$pager}
		{/if}
	</div>
</div>