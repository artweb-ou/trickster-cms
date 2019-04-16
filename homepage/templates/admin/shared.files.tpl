{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->URL}" method="post" class="" enctype="multipart/form-data">
	<div class="form_fields">
		<div class="form_items">
			<div class="from_label">
				<h3>
					{translations name=$element->structureType|cat:'.files'}
				</h3>
			</div>
			<div class="form_field"></div>
		</div>
		<div class="form_items">
			<div class="form_label">{translations name=$element->structureType|cat:'.files_upload'}</div>
			<div class="form_field">
				<input class="fileinput_placeholder" type="file" name="{$formNames.connectedFile}[]" multiple="multiple" />
			</div>
		</div>
	</div>
	{include file=$theme->template('component.controls.tpl') action="receiveFiles"}
</form>

<form action="{$element->getFormActionURL()}" class="content_list_form" method="post" enctype="multipart/form-data">
	{if $element->getFilesList()}
	<div class='controls_block form_controls'>
		<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
		<input type="hidden" class="content_list_form_action" value="deleteElements" name="action" />
		{if isset($rootPrivileges.deleteElements)}
		<button type='submit' onclick='if (!confirm("{translations name='message.deleteselectedconfirm'}")) return false}'
				class='button important'>
			<span class="icon icon_delete"></span>{translations name='button.deleteselected'}</button>
		{/if}
	</div>
	{include file=$theme->template('shared.contentTable.tpl') contentList=$element->getFilesList()}
	{/if}
</form>