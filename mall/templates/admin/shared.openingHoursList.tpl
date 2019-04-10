<div class="content_list_block">
	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">

		{if $currentElement->getAllowedChildStructureTypes()}
			<div class='controls_block content_list_controls'>
				<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
				<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

				{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedChildStructureTypes()}
			</div>
		{/if}
		{include file=$theme->template('shared.contentTable.tpl')}
	</form>
	<div class="content_list_bottom">
		{if isset($pager) && $currentElement->getChildrenList()}
			{include file=$theme->template("pager.tpl") pager=$pager}
		{/if}
	</div>
</div>

{if $shops = $element->getShopsWithCustomOpeningHours()}

	<div class="content_list_block">
		<h1 class="content_list_title">
			{translations name='openinghours.shops'}
		</h1>
		<form action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
			{assign var='formNames' value=$rootElement->getFormNames()}
			{*  __ data table *}
			<table class='content_list'>
				<thead>
				<tr>
					<th class="name_column">
						{translations name='label.name'}
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
				</tr>
				</thead>
				<tbody>
				{foreach from=$shops item=contentItem}
					{if $contentItem->structureType != 'positions'}
						{assign var='typeName' value=$contentItem->structureType}
						{assign var='typeLowered' value=$contentItem->structureType|strtolower}
						{assign var='type' value="element."|cat:$typeLowered}
						{assign var='privilege' value=$privileges.$typeName}
						<tr class="content_list_item elementid_{$contentItem->id}">
							<td class='name_column'>
								<a href="{$contentItem->URL}">
									<span class='icon icon_{$contentItem->structureType}'></span>{if $contentItem->title}{$contentItem->title}{else}{$contentItem->structureName}{/if}
								</a>
							</td>
							<td class="edit_column">
								{if isset($privilege.showForm) && $privilege.showForm}
									<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
								{/if}
							</td>
							<td class='type_column'>
								{translations name=$type}
							</td>
							<td>
								{$contentItem->dateModified}
							</td>
						</tr>
					{/if}
				{/foreach}
				</tbody>
			</table>

		</form>
	</div>
{/if}