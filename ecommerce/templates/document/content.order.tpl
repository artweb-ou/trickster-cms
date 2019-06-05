{assign var='paymentBank' value=$data.paymentBank}
<div class="invoice_header_block">
	<table class="invoice_header_table">
		<tr>
			<td class="invoice_header_logo_info">
				<div class="invoice_header_logo">
					{if empty($logo)}
						{$logo = $theme->getImageUrl("logo.png")}
					{/if}
					<img class="logo_image" src="{$logo}">
				</div>
			</td>
			<td class="invoice_header_invoice_info">
				{if $data.documentType != 'orderConfirmation'}
					<div class="invoice_header_order_title">
						{if $data.documentType == 'advancePaymentInvoice'}
							{translations name='invoice.ordertitle_advancepaymentinvoice'}:
							<span class="invoice_header_order_number">{$data.advancePaymentInvoiceNumber}</span>
						{elseif $data.documentType == 'invoice'}
							{translations name='invoice.ordertitle_invoice'}:
							<span class="invoice_header_order_number">{$data.invoiceNumber}</span>
						{/if}
					</div>
				{/if}
				<div class="invoice_header_order_date">
					{translations name='invoice.orderdate'}: {$data.dateCreated}
				</div>

				{if $data.documentType == 'advancePaymentInvoice'}
					<div class="invoice_header_order_duedate">
						{translations name='invoice.orderduedate'}: {$data.dueDate}
					</div>
				{elseif $data.documentType == 'invoice'}
					<div class="invoice_header_order_paymentdate">
						{translations name='invoice.paymentdate'}: {$data.payment.date}
					</div>
				{/if}
			</td>
		</tr>
	</table>
</div>

<div class="order_top">
	{if $data.documentType == 'invoice'}
		{translations name='invoice.toptext_invoice' required=false}
	{elseif $data.documentType == 'orderConfirmation'}
		{translations name='invoice.toptext_orderconfirmation' required=false}
	{elseif $data.documentType == 'advancePaymentInvoice'}
		{translations name='invoice.toptext_advancepaymentinvoice' required=false}
	{/if}
</div>

<div class="invoice_top_block">
	<table class="invoice_top_table">
		<tr>
			<td class="invoice_top_payer_info">
				<div class="invoice_top_to">{translations name='invoice.payer'}:</div>
				{if $data.receiverIsPayer}
					{foreach from=$data.receiverFields item=fieldInfo}
						{$fieldInfo.title}: {$fieldInfo.value}
						<br />
					{/foreach}
				{else}
					{if $data.payerCompany}
						{$data.payerCompany} ({$data.payerFirstName} {$data.payerLastName})
						<br />
					{else}
						{$data.payerFirstName} {$data.payerLastName}
						<br />
					{/if}
					{if $data.payerAddress}
						{$data.payerAddress}{if $data.payerCity}, {$data.payerCity}{/if}{if $data.payerPostIndex}, {$data.payerPostIndex}{/if}
						<br />
					{/if}
					{if $data.payerCountry}{$data.payerCountry}<br />{/if}
					{if $data.payerEmail}{$data.payerEmail}<br />{/if}
					{if $data.payerPhone}{$data.payerPhone}<br />{/if}
				{/if}
			</td>
			<td class="invoice_top_receiver_info">
				{if $data.receiverFields}
					<div class="invoice_top_to">{translations name='invoice.receiver'}:</div>
					<div class="invoice_top_payer_row">
						{if !empty($data.receiverFields.company.value)}
							{$data.receiverFields.company.value} ({$data.receiverFields.firstName.value} {$data.receiverFields.lastName.value})
						{else}
							{$data.receiverFields.firstName.value} {$data.receiverFields.lastName.value}
						{/if}
					</div>
					{if !empty($data.receiverFields.address.value)}
						<div class="invoice_top_payer_row">
							{$data.receiverFields.address.value}{if $data.receiverFields.city.value}, {$data.receiverFields.city.value}{/if}{if $data.receiverFields.postIndex.value}, {$data.receiverFields.postIndex.value}{/if}
						</div>
					{/if}
					{if !empty($data.receiverFields.country.value)}
						<div class="invoice_top_payer_row">
							{$data.receiverFields.country.value}
						</div>
					{/if}
					{if !empty($data.receiverFields.email.value)}
						<div class="invoice_top_payer_row">
							{$data.receiverFields.email.value}
						</div>
					{/if}
					{if !empty($data.receiverFields.phone.value)}
						<div class="invoice_top_payer_row">
							{$data.receiverFields.phone.value}
						</div>
					{/if}
					{if !empty($data.receiverFields.other)}
					{foreach from=$data.receiverFields.other item=field}
						<div class="invoice_top_payer_row">{nl2br($field.value)}</div>
					{/foreach}
					{/if}
				{/if}
			</td>
			<td class="invoice_top_delimiter"></td>
			<td class="invoice_top_company_info">
				<div class="invoice_top_from">{translations name='invoice.from'}:</div>
				<div class="invoice_top_company_row">{translations name='invoice.companyaddress'}</div>
			</td>
		</tr>
	</table>
</div>
{if $data.documentType == 'advancePaymentInvoice'}
	<h1 class="order_heading">{translations name='invoice.ordertitle_advancepaymentinvoice'} {$data.advancePaymentInvoiceNumber}</h1>
{elseif $data.documentType == 'invoice'}
	<h1 class="order_heading">{translations name='invoice.ordertitle_invoice'} {$data.invoiceNumber}</h1>
{elseif $data.documentType == 'orderConfirmation'}
	<h1 class="order_heading">{translations name='invoice.ordertitle_confirmationinvoice'} {$data.orderConfirmationNumber}</h1>
{/if}
<table class="order_productstable">
	<tbody class="order_productstable_head">
	<tr>
		<td>
			{translations name='invoice.code'}
		</td>
		<td>
			{translations name='invoice.product'}
		</td>
		<td>
			{translations name='invoice.price'}
		</td>
		<td>
			{translations name='invoice.quantity'}
		</td>
		<td>
			{translations name='invoice.total'}
		</td>
	</tr>
	</tbody>
	<tbody class="order_productstable_data">
	{foreach from=$data.addedProducts item=productInfo}
		<tr>
			<td>
				{$productInfo.code}
			</td>
			<td>
				{$productInfo.title} {if $productInfo.variation}({$productInfo.variation}){/if}
			</td>
			<td style="white-space: nowrap;">
				{if !$productInfo.emptyPrice}
					{$productInfo.price} {$data.currency} {if $productInfo.unit} / {$productInfo.unit}{/if}
				{/if}
			</td>
			<td>
				{$productInfo.amount}
			</td>
			<td>
				{if !$productInfo.emptyPrice}
					{$productInfo.totalPrice} {$data.currency}
				{/if}
			</td>
		</tr>
	{/foreach}
	</tbody>
	{$displayTotals = false}
	{foreach $data.addedProducts as $product}
		{if !$product.emptyPrice}
			{$displayTotals = true}
		{/if}
	{/foreach}
	{if $displayTotals}
		<tfoot class="order_productstable_summary">
		<tr class="order_productstable_bigempty">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td class="order_productstable_cellempty" colspan="2"></td>
			<td class="order_productstable_label" colspan="2">
				{translations name='invoice.productstotal'}
			</td>
			<td class="order_productstable_value">
				{$data.productsPrice} {$data.currency}
			</td>
		</tr>
		{foreach $data.servicesList as $service}
			<tr>
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{$service.title}
				</td>
				<td class="order_productstable_value">
					{$service.price} {$data.currency}
				</td>
			</tr>
		{/foreach}
		{if $data.deliveryPrice !== ""}
			<tr>
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{translations name='invoice.deliveryprice'}{if $data.deliveryTitle} ({$data.deliveryTitle}){/if}
				</td>
				<td class="order_productstable_value">
					{$data.deliveryPrice} {$data.currency}
				</td>
			</tr>
		{/if}
		{foreach from=$data.discountsList item=discountInfo}
			<tr>
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{$discountInfo.title}
				</td>
				<td class="order_productstable_value">
					-{$discountInfo.value} {$data.currency}
				</td>
			</tr>
		{/foreach}
		{if !$data.pricesIncludeVat}
			<tr>
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{translations name='invoice.novat'}
				</td>
				<td class="order_productstable_value">
					{$data.noVatAmount} {$data.currency}
				</td>
			</tr>
			<tr>
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{translations name='invoice.vat'}
				</td>
				<td class="order_productstable_value">
					{$data.vatAmount} {$data.currency}
				</td>
			</tr>
			<tr class="order_productstable_summary_total">
				<td class="order_productstable_cellempty" colspan="2"></td>
				<td class="order_productstable_label" colspan="2">
					{translations name='invoice.ordertotalprice'}
				</td>
				<td class="order_productstable_value">
					{$data.totalPrice} {$data.currency}
				</td>
			</tr>
		{/if}
		</tfoot>
	{/if}
</table>
{if $data.pricesIncludeVat}
	<div class="order_pricesIncludeVat">
		{translations name="shoppingbasket.pricesincludevat"}
	</div>
{/if}
{if !empty($data.receiverFields.comment)}
	{if $data.receiverFields.comment.value}
		<div class="order_comment">
			{nl2br($data.receiverFields.comment.value)}
		</div>
	{/if}
{/if}
<div class="order_bottom">
	<p>
		{if $data.documentType == 'invoice'}
			{translations name='invoice.bottomtext_invoice' required=false}
		{elseif $data.documentType == 'orderConfirmation'}
			{translations name='invoice.bottomtext_orderconfirmation' required=false}
		{elseif $data.documentType == 'advancePaymentInvoice'}
			{translations name='invoice.bottomtext_advancepaymentinvoice' required=false}
		{/if}
	</p>
</div>
