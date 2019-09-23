{foreach from=$formData.$fieldName key=languageId item=valueTextarea}
	<div class="form_items{if !empty($item.class)} {$item.class}{/if}"{if !empty($item.style)} style="{$item.style}"{/if}>
		<span class="form_label">
			{translations name="{$translationGroup}.{strtolower($fieldName)}"} ({$languageNames.$languageId|lower}){if !empty($languageIcons)}<span class="flag_lang" style='background-image: url("{$controller->baseURL}image/type:languageFlag/id:{$languageIcons.$languageId[0]}/filename:{$languageIcons.$languageId[1]}")' title='{$languageNames.$languageId}'></span>{/if}
		</span>
		<div class="form_field">
			<textarea class="textarea_component{if !empty($item.textarea_class)} {$item.textarea_class}{/if}" type="text" name="{$formNames.$fieldName.$languageId}" >{$valueTextarea}</textarea>
		</div>
		{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name=$fieldName}
	</div>
{/foreach}