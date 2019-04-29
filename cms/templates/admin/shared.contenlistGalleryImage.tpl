<div class="content_list_block">
	<form class="content_list_form" action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
		<div class='controls_block content_list_controls'>

			{include file=$theme->template('block.buttons.tpl')}
		</div>

		<table class='content_list gallery_form_images_table'>
			<thead>
			<tr>
				<th class='checkbox_column'>
					<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
				</th>

				<th>
					{translations name='label.image'}
				</th>

				<th class="generic">
					{translations name='label.name'}
				</th>
				<th class="generic">
					{translations name='label.alt'}
				</th>
				<th class="generic">
					{translations name='label.description'}
				</th>
				<th class='edit_column'>
					{translations name='label.edit'}
				</th>
				<th class='delete_column'>
					{translations name='label.delete'}
				</th>
			</tr>
			</thead>

			<tbody class="gallery_form_images_list">
			{foreach from=$element->getIconsList() item=image name=imagesList}
				<tr>
					<td class="checkbox_cell">
						<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$image->id}]" value="1" />
					</td>

					<td class="gallery_form_image_imagecell">
						{if $image->originalName}
							<img class="gallery_form_image_image" src='{$controller->baseURL}image/type:adminImage/width:100/height:100/id:{$image->image}/filename:{$image->originalName}' />
						{/if}
					</td>

					<td class="generic">
						{$image->title}
					</td>

					<td class="generic gallery_form_image_alt">
						{$image->alt}
					</td>

					<td class="generic gallery_form_image_description">
						{$image->description}
					</td>

					<td class="edit_column">
						<a class='icon icon_edit' href="{$image->URL}id:{$image->id}/action:showForm"></a>
					</td>

					<td class="delete_column">
						<a onclick='if (!confirm("{translations name='message.deleteconfirm1'} \"{$image->getTitle()}\" {translations name='message.deleteconfirm2'}")) return false;
							}' href="{$image->URL}id:{$image->id}/action:delete" class='icon icon_delete'></a>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		<div class="content_list_bottom">
		</div>
	</form>
</div>