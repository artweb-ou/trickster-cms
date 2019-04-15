<div class="content_list_block">
	{include file=$theme->template("pager.tpl") pager=$currentElement->pager}

	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
		<div class='controls_block content_list_controls'>
			<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
			<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

			{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedTypes()}
		</div>
		{assign var='formNames' value=$rootElement->getFormNames()}
		{if $currentElement->paymentsList}
			{*  __ data table *}
		<table class='content_list'>
			<thead>
				<tr>
					<th class='checkbox_column'>
						<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
					</th>
					<th class="name_column">
						{translations name='label.paymentnumber'}
					</th>
					<th class='edit_column'>
						{translations name='label.edit'}
					</th>
					<th>
						{translations name='label.order_number'}
					</th>
					<th>
						{translations name='label.user'}
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
						{translations name='label.paymentstatus'}
					</th>
					<th class='date_column'>
						{translations name='payment.date_ordered'}
					</th>
					<th class='date_column'>
						{translations name='payment.date_payed'}
					</th>
					<th class='delete_column'>
						{translations name='label.delete'}
					</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$currentElement->paymentsList item=contentItem}
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
							{$contentItem->id}
						</a>
					</td>
					<td class="edit_column">
						{if $privilege.showForm}
							<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
						{/if}
					</td>
					<td>
                        {assign var="orderElement" value=$contentItem->getOrderElement()}
						{if $orderElement}
						<a href="{$orderElement->URL}">
							{$orderElement->getInvoiceNumber()}
						</a>
						{/if}
					</td>
					<td>
						{if $contentItem->userElement}
							<a href="{$contentItem->userElement->URL}">
								{$contentItem->userElement->userName}
							</a>
						{/if}
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
						{$contentItem->getStatusText()}
					</td>
					<td>
						{$orderElement->dateCreated}
					</td>
					<td>
						{$contentItem->date}
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
				{include file=$theme->template("pager.tpl") pager=$currentElement->pager}
			</div>
		{/if}
	</form>

</div>