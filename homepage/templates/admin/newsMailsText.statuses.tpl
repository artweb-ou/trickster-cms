{if count($element->historyList)}
{*{assign var='formData' value=$element->getFormData()}*}
{*{assign var='formErrors' value=$element->getFormErrors()}*}
{*{assign var='formNames' value=$element->getFormNames()}*}
{*<form action="{$element->getFormActionURL()}" class="form_component" class="newsmailstext_form" method="post" enctype="multipart/form-data">*}
{*<div class='newsmails_history'>*}
	{*<input class="button" type="submit" value='{translations name='button.startsending'}'/>*}
	{*<div class="newsmails_history_groups">*}
		{*{foreach from=$element->groupsList item=group}*}
			{*<div class="newsmails_history_group_row">*}
				{*<input class="newsmailstext_group groupelement_{$group->id}" type="checkbox" value="1" name="" /> {$group->title}*}
			{*</div>*}
		{*{/foreach}*}
	{*</div>*}

	{*{include file=$theme->template("pager.tpl") pager=$pager}*}
	<table class='content_list newsmails_history_table'>
		<thead>
			<tr>
				<td class="newsmails_history_table_email">
					{translations name='label.email'}
				</td>
				<td>
					{translations name='label.date'}
				</td>
				<td>
					{translations name='label.status'}
				</td>
			</tr>
		</thead>
		<tbody>
		{foreach from=$element->historyList item=historyItem}
			<tr>
				<td class="newsmails_history_table_email">
					{$historyItem.email}
				</td>
				<td>
					{date('d.m.Y H:i', $historyItem.startTime)}
				</td>
				<td>
					{if $historyItem.status == 'awaiting'}
						<span class='newsmails_status_awaiting'>
							{translations name='label.sending_awaiting'}
						</span>
					{elseif $historyItem.status == 'success'}
						<span class='newsmails_status_success'>
							{translations name='label.sending_success'}
						</span>
					{elseif $historyItem.status == 'fail'}
						<span class='newsmails_status_fail'>
							{translations name='label.sending_fail'}
						</span>
					{elseif $historyItem.status == 'cancelled'}
						<span class='newsmails_status_cancelled'>
							{translations name='label.sending_cancelled'}
						</span>
					{elseif $historyItem.status == 'inprogress'}
						<span class='newsmails_status_inprogress'>
							{translations name='label.sending_inprogress'}
						</span>
					{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	{*{include file=$theme->template("pager.tpl") pager=$pager}*}
	{*<input class="button" type="submit" value='{translations name='button.startsending'}'/>*}
	{*<input type="hidden" value="{$element->id}" name="id" />*}
	{*<input type="hidden" value="sendEmails" name="action" />*}
{*</div>*}
{*</form>*}
{/if}