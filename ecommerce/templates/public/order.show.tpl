{capture assign="moduleTitle"}
	{translations name='purchasehistory.ordergeneralinfo'}
{/capture}
{capture assign="moduleContent"}
	<table class="purchasehistory_order_table purchasehistory_order_info_table table_component">
		<tbody>
			<tr>
				<th class="purchasehistory_order_table_label">
					{translations name='purchasehistory.date'}
				</th>
				<td>
					{$element->dateCreated}
				</td>
			</tr>
			<tr>
				<th class="purchasehistory_order_table_label">
					{translations name='purchasehistory.ordernr'}
				</th>
				<td>
					{$element->invoiceNumber}
				</td>
			</tr>
			{if $element->orderConfirmationFile}
				<tr>
					<th class="purchasehistory_order_table_label">
						<b>{translations name='purchasehistory.pdf_orderconfirmation'}:</b>
					</th>
					<td>
						<a href="{$element->getPdfDownLoadUrl('orderConfirmation')}">{translations name='purchasehistory.download'}</a>
					</td>
				</tr>
			{/if}
			{if $element->advancePaymentInvoiceFile}
				<tr>
					<th class="purchasehistory_order_table_label">
						<b>{translations name='purchasehistory.pdf_advancepaymentinvoice'}:</b>
					</th>
					<td>
						<a href="{$element->getPdfDownLoadUrl('advancePaymentInvoice')}">{translations name='purchasehistory.download'}</a>
					</td>
				</tr>
			{/if}
			{if $element->invoiceFile}
				<tr>
					<th class="purchasehistory_order_table_label">
						<b>{translations name='purchasehistory.pdf_invoice'}:</b>
					</th>
					<td>
						<a href="{$element->getPdfDownLoadUrl('invoice')}">{translations name='purchasehistory.download'}</a>
					</td>
				</tr>
			{/if}
			<tr>
				<th class="purchasehistory_order_table_label">
					{translations name='purchasehistory.status'}
				</th>
				<td>
					{translations name='purchasehistory.orderstatus'|cat:$element->getOrderStatus()}
				</td>
			</tr>
			<tr>
				<th class="purchasehistory_order_table_label">
					{translations name='purchasehistory.deliveryprice'}
				</th>
				<td>
					{if $element->deliveryPrice !== ""}{number_format($element->deliveryPrice, 2, '.', '')} {$element->currency}{/if}
				</td>
			</tr>
			<tr>
				<th class="purchasehistory_order_table_label">
					{translations name='purchasehistory.paidamount'}
				</th>
				<td>
					{$element->paidAmount} {$element->currency}
				</td>
			</tr>
		</tbody>
	</table>

{/capture}

{assign moduleClass "purchasehistory"}
{include file=$theme->template("component.contentmodule.tpl")}

{if $element->getOrderProducts()}
	{capture assign="moduleTitle"}
		{translations name='purchasehistory.orderproducts'}
	{/capture}

	{capture assign="moduleContent"}
		<table class="purchasehistory_order_table purchasehistory_order_products_table table_component">
			<thead>
				<tr>
					<th>
						{translations name='purchasehistory.title'}
					</th>
					<th>
						{translations name='purchasehistory.productcode'}
					</th>
					<th>
						{translations name='purchasehistory.amount'}
					</th>
					<th>
						{translations name='purchasehistory.price'}
					</th>
					<th>
						{translations name='purchasehistory.totalprice'}
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$element->getOrderProducts() item=product}
					<tr>
						<td>
							{$product->title}
						</td>
						<td>
							{$product->id}
						</td>
						<td>
							{$product->amount}
						</td>
						<td>
							{$product->price} {$product->currency}
						</td>
						<td>
							{$product->getTotalPrice(true)} {$product->currency}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/capture}
	{assign moduleClass "purchasehistory"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}
{if count($element->getDiscountsList())}
	{capture assign="moduleTitle"}
		{translations name='purchasehistory.orderdiscounts'}
	{/capture}
	{capture assign="moduleContent"}
		<table class="purchasehistory_order_table purchasehistory_order_discounts_table table_component">
			<thead>
				<tr>
					<th>
						{translations name='purchasehistory.discountname'}
					</th>
					<th>
						{translations name='purchasehistory.discountvalue'}
					</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$element->getDiscountsList() item=discount}
					<tr>
						<td>
							{$discount->title}
						</td>
						<td>
							{$discount->value}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/capture}

	{assign moduleClass "purchasehistory"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}

{if $element->getOrderFields()}
	{capture assign="moduleTitle"}
		{translations name='purchasehistory.receiverdata'}
	{/capture}
	{capture assign="moduleContent"}
	<table class="purchasehistory_order_table purchasehistory_order_receiver_table table_component">
		<tbody>
			{foreach from=$element->getOrderFields() item=fieldElement}
				{assign var='fieldFormData' value=$fieldElement->getFormData()}
				<tr>
					<th class="purchasehistory_order_table_label">
						{$fieldElement->title}
					</th>
					<td>
						{$fieldFormData.value}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	{/capture}

	{assign moduleClass "purchasehistory"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}

{capture assign="moduleTitle"}
	{translations name='purchasehistory.payerdata'}
{/capture}
{capture assign="moduleContent"}
<table class="purchasehistory_order_table  purchasehistory_order_payer_table table_component">
	<tbody>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.company'}
			</th>
			<td>
				{$element->payerCompany}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.name'}
			</th>
			<td>
				{$element->payerFirstName} {$element->payerLastName}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.email'}
			</th>
			<td>
				{$element->payerEmail}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.phone'}
			</th>
			<td>
				{$element->payerPhone}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.city'}
			</th>
			<td>
				{$element->payerCity}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.address'}
			</th>
			<td>
				{$element->payerAddress}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.zipcode'}
			</th>
			<td>
				{$element->payerPostIndex}
			</td>
		</tr>
		<tr>
			<th class="purchasehistory_order_table_label">
				{translations name='purchasehistory.country'}
			</th>
			<td>
				{$element->payerCountry}
			</td>
		</tr>
	</tbody>
</table>
{/capture}

{assign moduleClass "purchasehistory"}
{include file=$theme->template("component.contentmodule.tpl")}