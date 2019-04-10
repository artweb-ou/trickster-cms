{if $element->hasActualStructureInfo()}
	{if !$element->getPaymentElement()}
		<div class='controls_block content_list_controls'>
			{include file=$theme->template('block.newelement.tpl') allowedTypes=array("payment") buttonId="addNewProduct"}
		</div>
	{else}
	<table class="content_list">
		<thead>
			<tr>
				<th class='checkbox_column'>

				</th>
				<th>
					{translations name='label.paymentnumber'}
				</th>
				<th>
					{translations name='label.payeraccount'}
				</th>
				<th>
					{translations name='label.accountowner'}
				</th>
				<th>
					{translations name='label.bank'}
				</th>
				<th>
					{translations name='label.paymentamount'}
				</th>
				<th>
					{translations name='label.status'}
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

				</th>
			</tr>
		</thead>
		<tbody>
			{assign var=contentItem value=$element->getPaymentElement()}
			{if $contentItem->structureType != 'positions'}
				{assign var='typeName' value=$contentItem->structureType}
				{assign var='typeLowered' value=$contentItem->structureType|strtolower}
				{assign var='type' value="element."|cat:$typeLowered}
				{assign var='privilege' value=$privileges.$typeName}
				<tr class="content_list_item elementid_{$contentItem->id}">
					<td>
						<a href="{$contentItem->URL}" class='icon icon_{$contentItem->structureType}'></a>
					</td>
					<td class='name_column'>
						<a href="{$contentItem->URL}">
							{$contentItem->id}
						</a>
					</td>
					<td>
						{$contentItem->account}
					</td>
					<td>
						{$contentItem->payer}
					</td>
					<td>
						{$contentItem->bank}
					</td>
					<td>
						{$contentItem->amount} {$contentItem->currency}
					</td>
					<td>
						{translations name='label.payment_'|cat:$contentItem->paymentStatus}
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
							<a href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
						{/if}
					</td>
				</tr>
			{/if}
		</tbody>
	</table>

	{/if}
{/if}
