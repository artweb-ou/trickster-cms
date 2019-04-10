{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
{if $element->newImageForm}


	<div class="gallery_form">
			<div style="margin-top: 30px;">
				{if $element->newImageForm}
					{include file=$theme->template('galleryImage.form.tpl') element=$element->newImageForm}
				{/if}
				<form action="{$currentElement->getFormActionURL()}" method="post" enctype="multipart/form-data">
					<div class='controls_block content_list_controls'>
						<input type="hidden" value="{$element->id}" name="id" />
						<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />

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
						{foreach from=$element->getImagesList() item=image name=imagesList}
							<tr>
								<td class="checkbox">
									<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$image->id}]" value="1" />
									{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="elements"}
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

								<td>
									<a onclick='if (!confirm("{translations name='message.deleteconfirm1'} \"{if $image->title}{$image->title}{else}{$image->structureName}{/if}\" {translations name='message.deleteconfirm2'}")) return false;' href="{$image->URL}id:{$image->id}/action:delete" class='icon icon_delete'></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
				<div class="content_list_bottom">
				</div>
				</form>
			</div>
		</div>
{/if}