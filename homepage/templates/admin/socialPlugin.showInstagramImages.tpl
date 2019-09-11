{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<div class="content_list_block">
	<div class='content_list_controls'>
		<form method="GET" action="{$currentElement->getSocialActionUrl('updateInstagramImages')}">
			{foreach $currentElement->getPages() as $page}
				<label>
					<input type="checkbox" name="pagesSocialIds[]" value="{$page->socialId}" />
					<span>{$page->title}</span>
				</label><br />
			{/foreach}
			<br />
			{*<input type="hidden" value="{$element->id}" name="socialPostId" />*}
			<input type="hidden" value="{$currentElement->URL}id:{$currentElement->id}/action:updateInstagramImages/" name="return" />
			<button type="submit" class="button">
				<span class="icon icon_move"></span>
				{translations name='label.import'}
			</button>
		</form>
	</div>
	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
		{if !empty($socialErrorMessage)}
			{$socialErrorMessage}
		{/if}
		{if $images = $currentElement->getInstagramImages()}
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
						{translations name='label.socialPage'}
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
				{foreach from=$images item=contentItem}
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
							{if $page = $currentElement->getPageBySocialId($contentItem->pageSocialId)}
								{$page->title}
							{/if}
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

