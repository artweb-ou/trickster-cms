{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
{$optionsImages = $element->getOptionsImagesInfo()}

<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data" class="product_form">
	{foreach $element->getBasketSelectionsInfo() as $selection}
		<table class="form_table">
			<tr>
				<td colspan='2'>
					<h1 class="form_inner_title">{$selection.title}</h1>
				</td>
			</tr>
			{foreach $selection.productOptions as $option}
			<tr>
				<td class="form_label">
					{$option.title}:
				</td>
				<td>
					<select class="dropdown_placeholder" name="{$formNames.optionsImagesInput}[{$option.id}]" autocomplete='off'>
						<option value=""></option>
						{foreach $element->getImagesList() as $image}
							<option value="{$image->id}"{if isset($optionsImages[$option.id]) && $optionsImages[$option.id] == $image->id} selected="selected"{/if}>
								{$image->getTitle()}
							</option>
						{/foreach}
					</select>
				</td>
			</tr>
			{/foreach}
		</table>
	{/foreach}
	{include file=$theme->template('component.controls.tpl') action='receiveOptionsImagesForm'}
</form>
