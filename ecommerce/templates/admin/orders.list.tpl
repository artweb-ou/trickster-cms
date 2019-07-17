<div class="orders_list">
	<div class="orders_list_filtration filtration_component">
		<form class='panel_component orders_list_filtration_form filtration_form' action="{$element->URL}id:{$element->id}/action:filter/" method="POST">
			<div class="panel_heading">
				{translations name='orderslist.selectdates'}
			</div>
			<div class="panel_content filtration_form_items">
				<div class="filtration_form_item">
					<div class="filtration_form_item_field">
						<select class="dropdown_placeholder orders_list_filtration_preset">
							{foreach from=$element->filterSelector item=option name=options}
								<option value="{$option.value}" {if $smarty.foreach.options.last}selected="selected"{/if}>{$option.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="filtration_form_item">
					<div class="filtration_form_item_field">
						<span class="date_container">
							<input class="input_component orders_list_filtration_start input_date" type="text" value="" />
							<span class="icon icon_calendar"></span>
						</span>

					</div>
				</div>
				<div class="filtration_form_item">
					<div class="filtration_form_item_field">
						<span class="date_container">
							<input class="input_component orders_list_filtration_end input_date" type="text" value="" />
							<span class="icon icon_calendar"></span>
						</span>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="orders_list_heading controls_block content_list_controls">


		{include file=$theme->template('block.newelement.tpl') allowedTypes=$currentElement->getAllowedTypes()}
		<a class="orders_list_heading_exportxlsx button primary_button" href="">{translations name='orderslist.exportxlsx'}</a>
		<a class="orders_list_heading_displaypdf button" href="">{translations name='orderslist.viewpdf'}</a>
		<a class="orders_list_heading_downloadpdf button" href="">{translations name='orderslist.downloadpdf'}</a>
		<button class="orders_list_heading_new button primary_button">
			<span class="orders_list_heading_new_link ">{translations name='orderslist.neworders'}:
				<span class="orders_list_heading_new_value"></span></span>
		</button>
	</div>
	<div class="panel_component">
		<div class="panel_heading orders_list_heading_date">
			{translations name='orderslist.heading'}
			<span class="orders_list_heading_start"></span> - <span class="orders_list_heading_end"></span>
		</div>
		<div class="panel_content">
			<table class='table_component orders_list_table'>
				<thead>
				<tr>
					<th>
						Nr.
					</th>
					<th>
						{translations name='orderslist.ordernumber'}
					</th>
					<th>
						{translations name='orderslist.orderer'}
					</th>
					<th>
						{translations name='orderslist.payedamount'}
					</th>
					<th>
						{translations name='orderslist.deliveryprice'}
					</th>
					<th>
						{translations name='orderslist.discount'}
					</th>
					<th>
						{translations name='orderslist.productsprice'}
					</th>
					<th>
						{translations name='orderslist.totalprice'}
					</th>
					<th>
						{translations name='label.status'}
						<select class="dropdown_placeholder orders_list_table_filter">
							<option value="payed;undefined;sent;failed;paid_partial">{translations name='orderslist.status_all'}</option>
							<option value="payed;undefined">{translations name='orderslist.status_new'}</option>
							<option value="payed">{translations name='orderslist.status_payed'}</option>
							<option value="undefined">{translations name='orderslist.status_undefined'}</option>
							<option value="failed">{translations name='orderslist.status_failed'}</option>
							<option value="sent">{translations name='orderslist.status_sent'}</option>
							<option value="deleted">{translations name='orderslist.status_deleted'}</option>
							<option value="paid_partial">{translations name='orderslist.status_paid_partial'}</option>
						</select>
					</th>
					<th>
						{translations name='label.statuschange'}
					</th>
					<th>
						{translations name='label.date'}
					</th>
					<th class='delete_column'>
						{translations name='label.delete'}
					</th>
				</tr>
				</thead>
				<tbody class="content_list_item">
				</tbody>
				<tfoot class="orders_list_table_footer">
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td class="orders_list_table_footer_name">
						{translations name='orderslist.total'}:
					</td>
					<td class="orders_list_table_footer_name">
						{translations name='orderslist.total'}:
					</td>
					<td class="orders_list_table_footer_name">
						{translations name='orderslist.total'}:
					</td>
					<td class="name_column"></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td class="orders_list_table_payedtotal"></td>
					<td class="orders_list_table_deliverytotal"></td>
					<td class="orders_list_table_totalprice"></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				</tfoot>
			</table>
		</div>

	</div>

</div>