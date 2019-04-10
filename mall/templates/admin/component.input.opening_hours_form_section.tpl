{if empty($formFieldName)}
    {$formFieldName = 'hoursData'}
{/if}
<div class="form_items">
    <div class="form_label">
        {translations name="{$structureType}.opening_hours"}
    </div>
    <div class="form_field"></div>
</div>
{$days = [0,1,2,3,4,5,6]}
{foreach $days as $day}
    <div class="openinghours_form_day form_items">
        <div class="cell">
            <span class="form_label">
                {translations name="calendar.weekday_abbreviation_{$day+1}"}:
            </span>
        </div>
        <div class="form_field openinghours_form_day_controls cell">
            <div class="openinghours_container_input">
                <input class="openinghours_form_day_time_input input_component" type="text"
                       value="{if !empty($formData.$formFieldName.$day)}{$formData.$formFieldName.$day.start}{/if}"
                       name="{$formNames.$formFieldName}[{$day}][start]"/> -
                <input class="openinghours_form_day_time_input input_component" type="text"
                       value="{if !empty($formData.$formFieldName.$day)}{$formData.$formFieldName.$day.end}{/if}"
                       name="{$formNames.$formFieldName}[{$day}][end]"/>
                <div class="openinghours_form_day_controls_closed">
                    <input class="singlebox checkbox_placeholder" id="openinghours_form_day_controls_closed_day_{$day}"
                           type="checkbox" name="{$formNames.$formFieldName}[{$day}][closed]"
                           value="1"{if !empty($formData.$formFieldName.$day) && !empty($formData.$formFieldName.$day.closed)} checked="checked"{/if}/>
                    <label class="openinghours_form_day_controls_closed_label"
                           for="openinghours_form_day_controls_closed_day_{$day}">{translations name="openinghours_group.day_closed"}</label>
                </div>
            </div>
        </div>
    </div>
{/foreach}