<div class="email_content">
	<h1>{$data.heading}</h1>
	<table class="form_table">
		<tbody>
		{foreach from=$data.groups item=groupElement}
			<tr>
				<td>
					<h2>{$groupElement.title}</h2>
				</td>
			</tr>
			{foreach from=$groupElement.formFields item=formField}
				<tr>
					<td class="form_label {if is_array($formField.fieldValue)}form_label_vertical_top{/if}">
						{$formField.fieldTitle}:
					</td>
					<td class="form_value">
						{if $formField.fieldType == 'input' || $formField.fieldType == 'textarea' || $formField.fieldType == 'select' || $formField.fieldType == 'dateInput'}
							{if is_array($formField.fieldValue)}
								{foreach from=$formField.fieldValue item=selectedItem}
									<div class="form_value_multiple">{$selectedItem}</div>
								{/foreach}
							{else}
								{if $formField.fieldSubType == 'pageUrl'}
									<a target="_blank" href="{$data.baseUrl}{$formField.fieldValue}">{$formField.fieldValue}</a>
								{else}
									{$formField.fieldValue}
								{/if}
							{/if}
						{elseif $formField.fieldType == 'checkbox'}
							{if $formField.fieldValue == '1'}&#9745;{else}&#9744;{/if}
						{/if}
					</td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="2"></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>