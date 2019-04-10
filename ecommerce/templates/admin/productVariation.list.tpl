{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data">
	<table class='product_variations_table'>
		<tr>
			<th>
				{translations name='field.code'}
			</th>
			{foreach from=$parentElement->selectedParameters key=parameterId item=parameter}
				{assign var='parameterInfo' value=$formData.parameters.$parameterId}
				{foreach from=$parameterInfo key=languageId item=info}
					<th>
						{$info.title}{if $languageId!='0'} ({$languageNames.$languageId}){/if}
					</th>
				{/foreach}
			{/foreach}
			<th>
				{translations name='field.price'}
			</th>
			<th>

			</th>
			<th>

			</th>
		</tr>
		{foreach from=$parentElement->variations item=productVariation}
			<tr>
				<td>
					{$productVariation->code}
				</td>
				{foreach from=$parentElement->selectedParameters key=parameterId item=parameter}
					{assign var='parameterInfo' value=$productVariation->formData.parameters.$parameterId}
					{foreach from=$parameterInfo key=languageId item=info}
						<td>
							{$info.value}
						</td>
					{/foreach}
				{/foreach}
				<td>
					{$productVariation->price}
				</td>
				<td>
					<a href="{$productVariation->URL}id:{$productVariation->id}/action:showForm" class='icon edit'></a>
				</td>
				<td>
					<a href="{$productVariation->URL}id:{$productVariation->id}/action:delete">{translations name='label.delete'}</a>
				</td>
			</tr>
		{/foreach}
		<tr>
			<td>
				<input type="text" value="{$formData.code}" name="{$formNames.code}" />
			</td>
			{foreach from=$parentElement->selectedParameters key=parameterId item=parameter}
				{assign var='parameterInfo' value=$formData.parameters.$parameterId}
				{foreach from=$parameterInfo key=languageId item=info}
					<td>
						<input size='6' type="text" value="{$info.value}" name="{$formNames.parameters}[{$parameterId}][{$languageId}]" />
					</td>
				{/foreach}
			{/foreach}
			<td>
				<input type="text" value="{$formData.price}" name="{$formNames.price}" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" value='{translations name='button.save'}' />
				<input type="hidden" value="{$element->id}" name="id" />
				<input type="hidden" value="receive" name="action" />
			</td>
		</tr>
	</table>
</form>
