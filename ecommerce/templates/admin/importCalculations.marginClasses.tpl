{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="importcalculations_form form_component" method="post" enctype="multipart/form-data">

		<table class="form_table importcalculations_form_table">
			<thead>
				<tr>
					<th></th>
					{foreach $element->getImportPlugins() as $pluginElement}
						<th>
							{$pluginElement->title}
						</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
				{foreach $element->getMarginClasses() as $classInfo}
					<tr>
						<th class="form_label">
							{$classInfo.fromPrice} - {$classInfo.toPrice}:
						</th>
						{foreach $element->getImportPlugins() as $pluginElement}
							<td>
								<input class='input_component' type="text" name="{$formNames.marginClassesInput}[{$classInfo.fromPrice}-{$classInfo.toPrice}][{$pluginElement->id}]" value="{if isset($classInfo.plugins[$pluginElement->id])}{$classInfo.plugins[$pluginElement->id]}{/if}">
							</td>
						{/foreach}
					</tr>
				{/foreach}
				<tr>
					<td class="form_label">
						<input class='input_component importcalculations_input_new_from' type="text" name="{$formNames.newMarginClassInput}[fromPrice]" value="">
						<input class='input_component importcalculations_input_new_to' type="text" name="{$formNames.newMarginClassInput}[toPrice]" value="">
					</td>
					{foreach $element->getImportPlugins() as $pluginElement}
						<td>
							<input class='input_component' type="text" name="{$formNames.newMarginClassInput}[plugins][{$pluginElement->id}]" value="" placeholder="%">
						</td>
					{/foreach}
				</tr>
			</tbody>
		</table>

		{include file=$theme->template('component.controls.tpl') action='receiveMarginClasses'}

</form>

