{if !isset($item.textClass)}
    {$item.textClass = ''}
{/if}

<div class="form_items{if $formErrors.$fieldName} form_error{/if}{if !empty($item.trClass)} {$item.trClass} {/if}">
		<span class="form_label">
			{translations name="{$translationGroup}.{strtolower($fieldName)}"}
		</span>
        <div class="form_field">
			<input class="input_component{if $item.textClass} {$item.textClass}{/if}" type="text" value="{$formData.$fieldName}"
		   name="{$formNames.$fieldName}"/>
		</div>
        <div class="form_helper">
			{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name=$fieldName}
		</div>
</div>
