{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

{$primarySelectionInfo = $element->getSelectionInfoForPricingForm(0)}
{$secondarySelectionInfo = $element->getSelectionInfoForPricingForm(1)}
{$pricings = $element->getSelectionsPricingsMap()}

{function printTable groupCode=''}
	<table class="product_options_pricing_form_table">
		{if $secondarySelectionInfo}
		<thead>
			<tr>
				<th>
				</th>
				{foreach $secondarySelectionInfo as $option}
					<th>{$option.title}</th>
				{/foreach}
			</tr>
		</thead>
		{/if}
		<tbody>
			{foreach $primarySelectionInfo as $option}
				<tr>
					<th>{$option.title}</th>
					{foreach $secondarySelectionInfo as $secondaryOption}
						{$comboCode = "{$option.code}{$secondaryOption.code}{$groupCode}"}
						<td>
							<input class="input_component" name="{$formNames.optionsPricingInput}[{$comboCode}]" placeholder="" value="{$element->getSelectionPriceByUncertainCombo($comboCode)}"/>
						</td>
					{foreachelse}
						{$comboCode = "{$option.code}{$groupCode}"}
						{$value = ''}
						{if isset($pricings.$comboCode)}
							{$value = $pricings.$comboCode}
						{/if}
						<td>
							<input class="input_component" name="{$formNames.optionsPricingInput}[{$comboCode}]" placeholder="" value="{$element->getSelectionPriceByUncertainCombo($comboCode)}"/>
						</td>
					{/foreach}
				</tr>
			{/foreach}
		</tbody>
	</table>
{/function}

<form action="{$element->getFormActionURL()}" class="form_component product_options_pricing_form" method="post" enctype="multipart/form-data" class="product_form">
	<div class="product_options_pricing_form_contents">
		{$groups = $element->getOptionsComboGroupsForPricingForm()}
		{foreach $groups as $group}
			<h3 class="form_inner_title product_options_pricing_form_group">
				{$group.title}
			</h3>
			{call name=printTable groupCode=$group.code}
		{foreachelse}
			{call name=printTable}
		{/foreach}
	</div>
	{include file=$theme->template('component.controls.tpl') action='receiveOptionsPricingForm'}
</form>
