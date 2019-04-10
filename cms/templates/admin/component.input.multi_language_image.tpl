{foreach from=$formData.$fieldName key=languageId item=image}
    <div class="form_items{if $formErrors.$fieldName.$languageId} form_error{/if}">
        <span class="form_label">
            {translations name="{$structureType}.{$fieldName}"} ({$languageNames.$languageId}):
        </span>
        <div class="form_field">
            {if $formData.$fieldName.$languageId != ""}
                <img src='{$controller->baseURL}image/type:adminImage/id:{$image}/filename:{$formData.originalName.$languageId}' />
                <br />
                <a href="{$element->URL}id:{$element->id}/action:deleteImage/language:{$languageId}/">{translations name="{$structureType}.deleteimage"}</a>
            {else}
                <input class="fileinput_placeholder" type="file" name="{$formNames.image.$languageId}" />
            {/if}
        </div>
    </div>
{/foreach}