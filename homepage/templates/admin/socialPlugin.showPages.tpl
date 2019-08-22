<div class="content_list_block">

	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
		<div class='controls_block content_list_controls'>
			{capture assign="returnUrl"}{$currentElement->getURL()}id:{$currentElement->id}/action:updateSocialPages/{/capture}
			<a class="actions_form_button button" href="{$currentElement->getSocialActionUrl('updatePages', $returnUrl)}">
				<span class="icon icon_move"></span>
				{translations name='label.update'}
			</a>
		</div>
		{if !empty($socialErrorMessage)}
			{$socialErrorMessage}
		{/if}
		{if $pages = $currentElement->getPages()}
			<table class='content_list'>
				<thead>
				<tr>
					<th class='checkbox_column'>
						<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
					</th>
					<th class="name_column">
						{translations name='label.name'}
					</th>
					<th>
						{translations name='social.id'}
					</th>
					<th class='edit_column'>
						{translations name='label.edit'}
					</th>
					<th class='date_column'>
						{translations name='label.dateCreated'}
					</th>
					<th class='date_column'>
						{translations name='label.dateModified'}
					</th>
					<th class='delete_column'>
						{translations name='label.delete'}
					</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$pages item=contentItem}
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
								{stripdomspaces}
									<span class='icon icon_{$contentItem->structureType}'></span>
									<span class="content_item_title">
									{$contentItem->getTitle()}
                            </span>
								{/stripdomspaces}
							</a>
						</td>
						<td class='view_column'>
							{$contentItem->socialId}
						</td>
						<td class='edit_column'>
							{if $privilege.showForm}
								<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
							{/if}
						</td>
						<td class='date_column'>
							{$contentItem->dateCreated}
						</td>
						<td class='date_column'>
							{$contentItem->dateModified}
						</td>
						<td>
							{if $privilege.delete}
								<a href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		{/if}
	</form>

	<div class="content_list_bottom">
	</div>

</div>

