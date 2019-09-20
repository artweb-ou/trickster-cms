
{if $element->hasActualStructureInfo()}
	<div class='controls_block content_list_controls'>
		{include file=$theme->template('block.newelement.tpl') allowedTypes=array("orderProduct")}
	</div>
	{if $element->getOrderProducts()}
	<table class='content_list order_products'>
		<thead>
			<tr>
				<th class="name_column">
					{translations name='field.orderproduct_title'}
				</th>
				<th>
					{translations name='field.orderproduct_code'}
				</th>
				<th>
					{translations name='field.orderproduct_price'}
				</th>
				<th>
					{translations name='field.orderproduct_amount'}
				</th>
				<th>
					{translations name='field.orderproduct_totalprice'}
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
					{translations name='label.delete'}
				</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$currentElement->getOrderProducts() item=contentItem}
			{if $contentItem->structureType != 'positions'}
			{assign var='typeName' value=$contentItem->structureType}
			{assign var='typeLowered' value=$contentItem->structureType|strtolower}
			{assign var='type' value="element."|cat:$typeLowered}
			{assign var='privilege' value=$privileges.$typeName}
			<tr class="content_list_item elementid_{$contentItem->id}">
				<td class='name_column'>
					{*<a href="{$contentItem->URL}">*}
						{*{if $contentItem->title}{$contentItem->title}{else}{$contentItem->structureName}{/if}*}
					{*</a>*}
					<span class='icon icon_{$contentItem->structureType}'></span><a href="{$contentItem->URL}">{if $contentItem->title}{$contentItem->title}{else}{$contentItem->structureName}{/if}</a>
				</td>
				<td>
					<a href="{$contentItem->URL}">
						{$contentItem->code}
					</a>
				</td>

				<td>
					{$contentItem->getPrice()} {$element->currency}
				</td>
				<td>
					{$contentItem->amount}
				</td>
				<td>
					{$contentItem->getTotalPrice(true)} {$element->currency}
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
						<a onclick='if (!confirm("{translations name='message.deleteconfirm1'} \"{if $contentItem->title}{$contentItem->title}{else}{$contentItem->structureName}{/if}\" {translations name='message.deleteconfirm2'}")) return false;' href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete'></a>
					{/if}
				</td>
			</tr>
			{/if}
		{/foreach}
		</tbody>
	</table>
	<div class="content_list_bottom">
	</div>
	{/if}
{/if}