{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}


<form class="productimport_form form_component" action="{$element->getFormActionURL()}" method="post" enctype="multipart/form-data">
	<div class="form_fields">
		<div class="form_items">
			<span class="form_label">
				{translations name='import.template'}
			</span>
			<div class="form_field">
				<select class="dropdown_placeholder" name="{$formNames.templateId}">
					{foreach from=$element->getChildrenList() item=template}
						<option value="{$template->id}"{if $formData.templateId == $template->id} selected="selected"{/if}>{$template->title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form_items">
			<span class="form_label">
				{translations name='import.category'}
			</span>
			<div class="form_field ">
				<select class="productimport_form_category_search" name="{$formNames.categoryId}" autocomplete='off'>
					<option value='{$formData.categoryId}' selected="selected">
						{$category->title}
					</option>
				</select>
			</div>
		</div>
		<div class="form_items">
			<span class="form_label">
				{translations name='import.language'}
			</span>
			<div class="form_field">
				<select class="dropdown_placeholder prices_new_selector" name="{$formNames.languageCode}">
					<option value=""></option>
					{foreach from=$languagesList item=language}
						<option value="{$language->iso6393}"{if $language->selected} selected="selected"{/if}>{$language->title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form_items">
			<span class="form_label">
				{translations name='import.csvupload'}
			</span>
			<div class="form_field">
				<input class="fileinput_placeholder" type="file" name="{$formNames.importFile}" />
			</div>
		</div>
	</div>
	<div class="controls_block form_controls">
		<input class="button button success_button" type="submit" value="{translations name='import.import'}" />
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="import" name="action" />
	</div>
</form>
