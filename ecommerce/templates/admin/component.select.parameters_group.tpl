<div class="form_items{if $formErrors.$fieldName} form_error{/if}">
	<span class="form_label">
		{translations name="{$structureType}.{$fieldName}"}
	</span>
	<div class="form_field">
		<select class="select_multiple" name="{$formNames.parameters}[]" multiple="multiple">
			<option value=""></option>
			{foreach from=$element->allParametersGroups item=group}
				<optgroup label="{$group->getTitle()}">
					{foreach from=$group->getParametersList() item=parameterInfo}
						<option value="{$parameterInfo->id}" {if $parameterInfo->selected2}selected='selected'{/if}>{$parameterInfo->getTitle()}</option>
					{/foreach}
				</optgroup>
			{/foreach}
		</select>
	</div>
</div>