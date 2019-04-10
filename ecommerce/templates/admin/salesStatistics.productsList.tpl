{if !isset($formElement)}{$formElement = $rootElement}{/if}
{assign 'formNames' $formElement->getFormNames()}
{if !isset($contentList)}
	{assign 'contentList' $currentElement->getChildrenList()}
{/if}
{if $contentList}
	<table class='content_list'>
		<thead>
		<tr>
			<th class='checkbox_column'>
				<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
			</th>
			<th class="name_column">
				{translations name='sales_statistics.product_code'}
			</th>
			<th class="name_column">
				{translations name='sales_statistics.product_name'}
			</th>
			<th class='sales_statistics_value'>
				{translations name='sales_statistics.product_current_price'}
			</th>
			<th class='sales_statistics_value'>
				{translations name='sales_statistics.product_ordered'}
			</th>
			<th class='sales_statistics_value'>
				{translations name='sales_statistics.product_total_price'}
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
					{$contentItem->code}
				</td>
				<td class='name_column'>
					<a href="{$contentItem->URL}">
						{$contentItem->getTitle()}
					</a>
				</td>
				<td class='sales_statistics_value'>
					{$contentItem->getPrice()} {$symbol}
				</td>
				<td class='sales_statistics_value'>
					{$currentElement->getProductCount($contentItem->id)}
				</td>
				<td class="sales_statistics_value">
					{$currentElement->getProductTotal($contentItem->id)} {$symbol}
				</td>
			</tr>
		{/foreach}
			<tr>
				<td colspan="4"></td>
				<td class='sales_statistics_value'>
					{$currentElement->getProductsTotalQuantity()}
				</td>
				<td class='sales_statistics_value'>
					{$currentElement->getProductsTotal()} {$symbol}
				</td>
			</tr>
		</tbody>
	</table>
{/if}
