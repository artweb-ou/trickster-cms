<div class="content_list_block">
	{if isset($pager)}
		{include file=$theme->template("pager.tpl") pager=$pager}
	{/if}

	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
		{if $currentElement->getAllowedTypes()}
			<div class='controls_block content_list_controls'>
				<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
				<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

				{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedTypes()}
			</div>
		{/if}
		{assign 'formNames' $rootElement->getFormNames()}
		{if $comments}
			<table class='content_list'>
				<thead>
				<tr>
					<th class='checkbox_column'>
						<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
					</th>
					<th class='edit_column'>
						{translations name='label.edit'}
					</th>
					<th class="name_column">
						{translations name='label.author'}
					</th>
					<th class='type_column'>
						{translations name='label.content'}
					</th>
					<th class="name_column">
						{translations name='label.target_element'}
					</th>
					<th class='date_column'>
						{translations name='label.date'}
					</th>
					<th class=''>
						{translations name='label.approved'}
					</th>
					<th class=''>
						{translations name='label.ip'}
					</th>
					<th class='delete_column'>
						{translations name='label.delete'}
					</th>
				</tr>
				</thead>
				<tbody>
				{foreach $comments as $comment}
					{if $comment}
						{assign var='typeName' value=$comment->structureType}
						{assign var='typeLowered' value=$comment->structureType|strtolower}
						{assign var='type' value="element."|cat:$typeLowered}
						{assign var='privilege' value=$privileges.$typeName}
						<tr class="content_list_item elementid_{$comment->id}">
							<td class="checkbox_cell">
								<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$comment->id}]" value="1" />
							</td>
							<td class='edit_column'>
								{if isset($privilege.showForm) && $privilege.showForm}
									<a href="{$comment->URL}id:{$comment->id}/action:showForm" class='icon icon_edit'></a>
								{/if}
							</td>
							<td class='name_column'>
								{if isset($privilege.showForm) && $privilege.showForm}
									<a href="{$comment->URL}id:{$comment->id}/action:showForm">{$comment->getAuthorName()}</a>
								{else}
									{$comment->getAuthorName()}
								{/if}
							</td>
							<td class='comments_content_column' width="20%">
								{mb_substr($comment->content, 0, 100)}
								{if mb_strlen($comment->content) > 100}...{/if}
							</td>
							<td class='name_column'>
								{if $target = $comment->getTarget()}
									{stripdomspaces}
										<span class='icon icon_{$target->structureType}'></span>
										<span class="content_item_title">
                                            <a href="{$target->URL}">{$target->getTitle()}</a>
                                        </span>
									{/stripdomspaces}
								{/if}
							</td>
							<td>
								{$comment->dateCreated}
							</td>
							<td>
								{if $comment->approved}
									{translations name='label.approved_yes'}
								{/if}
							</td>
							<td>
								{$comment->ipAddress}
							</td>
							<td class="delete_column">
								{if isset($privilege.delete) && $privilege.delete}
									<a href="{$comment->URL}id:{$comment->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
								{/if}
							</td>
						</tr>
					{/if}
				{/foreach}
				</tbody>
			</table>
		{/if}

	</form>
	<div class="content_list_bottom">
		{if isset($pager)}
			{include file=$theme->template("pager.tpl") pager=$pager}
		{/if}
	</div>
</div>