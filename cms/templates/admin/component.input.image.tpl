{if !isset($item.imageClass)}
    {$item.imageClass = ''}
{/if}
{if !isset($item.preset)}
    {$item.preset = 'adminImage'}
{/if}
<div class="form_field_{$fieldName}{if $formErrors.$fieldName} form_error {/if} form_items">
    <span class="form_label">
        {translations name="{$translationGroup}.{strtolower($fieldName)}"}
    </span>
    <div class="form_field">
        <input class="fileinput_placeholder" type="file" name="{$formNames.$fieldName}"/>
        <div class="form_image_component{if !$element->$fieldName} form_image_component_hidden{/if}">
            {if $element->$fieldName}
                <img class="form_image {$item.imageClass}"
                    src='{$controller->baseURL}image/type:{$item.preset}/id:{$element->$fieldName}/filename:{if !empty($item.filename) && !empty($formData.{$item.filename})}{$formData.{$item.filename}}{else}{$formData.originalName}{/if}'/>
                <a class="form_image_delete_button" href="{$element->URL}id:{$element->id}/action:deleteFile/file:{$fieldName}/">
                </a>
            {/if}
        </div>
    </div>
</div>