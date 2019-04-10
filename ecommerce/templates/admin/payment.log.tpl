{$records = $element->getRecords()}
{if $records}
	<table class="payment_log_table content_list">
		<thead>
			<tr>
				<th class="payment_log_cell_time">
					Time
				</th>
				<th class="payment_log_cell_direction">
					Direction
				</th>
				<th class="payment_log_cell_details">
					Details
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $records as $record}
				<tr>
					<td class="payment_log_cell_time">
						{date('d.m.Y H:i', $record->getTime())}
					</td>
					<td class="payment_log_cell_direction">
						{if $record->isFromBank()}
							<strong>FROM</strong>
						{else}
							TO
						{/if}
					</td>
					<td class="payment_log_cell_details">
						{foreach $record->getDetails() as $line}
							<b>{ucfirst($line@key)}</b>: {$line|htmlentities}
							{if !$line@last}
							<br/>
							{/if}
						{/foreach}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

{/if}