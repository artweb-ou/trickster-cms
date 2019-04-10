{assign var='floors' value=$form->getElementProperty($item.property)}

<div class="form_items">
    <span class="form_label">
        {translations name="{$structureType}.{$fieldName}"}
    </span>
    <div class="form_field">
        <select class="select_multiple" name="{$formNames.$fieldName}[]" autocomplete='off' multiple="multiple">
            {foreach $floors as $floorInfo}
                <optgroup label="{$floorInfo.title}">
                    {if isset($floorInfo.$fieldName)}
                        {foreach $floorInfo.$fieldName as $roomInfo}
                            <option value='{$roomInfo.id}'{if $roomInfo.select} selected="selected"{/if}>
                                {$roomInfo.title}
                            </option>
                        {/foreach}
                    {/if}
                </optgroup>
            {/foreach}
        </select>
    </div>
</div>