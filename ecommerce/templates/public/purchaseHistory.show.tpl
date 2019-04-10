{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}

{capture assign="moduleContent"}
	{if $orders}
		<table class="purchasehistory_table table_component">
			<thead>
				<tr>
					<th class="purchasehistory_th_number">#</th>
					<th class="purchasehistory_th_file">
						{translations name='purchasehistory.file'}
					</th>
					<th class="purchasehistory_th_paid">
						{translations name='purchasehistory.paidamount'}
					</th>
					<th class="purchasehistory_th_delivery">
						{translations name='purchasehistory.deliverycost'}
					</th>
					<th class="purchasehistory_th_status">
						{translations name='purchasehistory.status'}
					</th>
					<th class="purchasehistory_th_date">
						{translations name='purchasehistory.date'}
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$orders item=order name=orders}
					<tr>
						<td>
							{$smarty.foreach.orders.iteration}
						</td>
						<td>
							<a href="{$order->URL}">{$order->invoiceNumber}</a>
						</td>
						<td>
							{$order->paidAmount} {$order->currency}
						</td>
						<td>
							{if $order->deliveryPrice !== ""}{number_format($order->deliveryPrice, 2, '.', '')} {$order->currency}{/if}
						</td>
						<td>
							{translations name='purchasehistory.orderstatus'|cat:$order->getOrderStatus()}
						</td>
						<td>
							{$order->dateCreated}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{translations name='purchasehistory.nohistory'}
	{/if}
{/capture}

{assign moduleClass "purchasehistory"}
{include file=$theme->template("component.contentmodule.tpl")}