{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="importcalculations_form form_component" method="post" enctype="multipart/form-data">
    <table class='form_table'>
        <thead>
            <tr>
                <th></th>
                {foreach $importPlugins as $plugin}
                    <th>
                        {$plugin->title}
                    </th>
                {/foreach}
            </tr>
        </thead>
        <tbody>
            {foreach $categories as $category}
                <tr>
                    <th style="padding-left: {$category->level}em">
                        {$category->title}
                    </th>
                    {foreach $importPlugins as $plugin}
                        <td>
                            <input class='input_component' type="text" value="{if isset($modifiersIndex[$category->id]) && isset($modifiersIndex[$category->id][$plugin->id])}{$modifiersIndex[$category->id][$plugin->id]}{/if}" name="{$formNames.pricingInput}[{$category->id}][{$plugin->id}]"/>
                        </td>
                    {/foreach}
                </tr>
            {/foreach}
        </tbody>
    </table>
    {include file=$theme->template('component.controls.tpl') action='receiveCalculations'}
</form>
