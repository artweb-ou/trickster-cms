{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

{function printCategories depth=0}
	<table class="form_table">

		{foreach $items as $item}
			<tr>
				<td class="form_label">
					{$item->title}:
				</td>
				<td colspan='2'>
					{if $item->code != ''}
						{* in some warehouses only the bottommost child has code *}
						<select class="dropdown_placeholder" name="{$formNames.categoriesInput}[{$item->code}][]">
							{$connectedCategoryId = $selectedPlugin->getCategoryIdByImportId($item->code)}
							<option value=""{if !$connectedCategoryId} selected="selected"{/if}></option>
							{foreach $categories as $category}
								<option value="{$category->id}"{if $connectedCategoryId == $category->id} selected="selected"{/if} style="padding-left: {$category->level}em">
									{$category->title}
								</option>
							{/foreach}
						</select>
					{/if}
				</td>
			</tr>
			{if !empty($item->children)}
				<tr>
					<td colspan="2">
						{call name=printCategories items=$item->children depth=$depth+1}
					</td>
				</tr>
			{/if}
		{/foreach}
	</table>
{/function}

{if $selectedPlugin}
	{$pluginCategoriesInfo = $selectedPlugin->getWarehouseCategories()}
	{if $pluginCategoriesInfo}
		<form action="{$element->getFormActionURL()}plugin:{$selectedPlugin->id}/" class="importcategories_form form_component" method="post" enctype="multipart/form-data">
			{call name=printCategories items=$pluginCategoriesInfo}

			{include file=$theme->template('component.controls.tpl') action='receiveCategories'}
		</form>
	{/if}
{/if}