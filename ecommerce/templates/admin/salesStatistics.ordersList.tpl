{if !isset($formElement)}{$formElement = $rootElement}{/if}
{assign 'formNames' $formElement->getFormNames()}
{if $contentList}
	<table class='content_list'>
		<thead>
		<tr>
			<th class='checkbox_column'>
				<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
			</th>
			<th class='name_column'>
				{translations name='sales_statistics.order_nr'}
			</th>
			<th class='name_column'>
				{translations name='sales_statistics.orderer'}
			</th>
			<th class="sales_statistics_value">
				{translations name='sales_statistics.order_products'}
			</th>
			<th class="sales_statistics_value">
				{translations name='sales_statistics.order_total'}
			</th>
			<th class='edit_column'>
				{translations name='label.edit'}
			</th>
			<th class='date_column'>
				{translations name='label.date'}
			</th>
		</tr>
		</thead>
		<tbody>
		{foreach $contentList as $contentItem}
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
						{$contentItem->getTitle()}
					</a>
				</td>
				<td class='name_column'>
					{$contentItem->getPayerName()}
				</td>
				<td class="sales_statistics_value">
					{$contentItem->getTotalAmount()}
				</td>
				<td class="sales_statistics_value">
					{$contentItem->getTotalPrice()} {$symbol}
				</td>
				<td class='edit_column'>
					{if isset($privilege.showForm) && $privilege.showForm}
						<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
					{/if}
				</td>
				<td class='date_column'>
					{$contentItem->dateCreated}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}
