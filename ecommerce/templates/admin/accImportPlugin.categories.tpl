<table class='form_table'>
	<tr>
		<td colspan="2">
			<h1 class="form_inner_title">
				{translations name='accimportplugin.segments'}:
			</h1>
		</td>
	</tr>
	{foreach $selectedPlugin->getSegments() as $segment}
		{$pluginCategoryId = $segment->SegmentId}
		<tr>
			<td class="form_label">
				{$segment->SegmentName}:
			</td>
			<td colspan='2'>
				<select class="dropdown_placeholder" name="{$formNames.categoriesInput}[{$pluginCategoryId}]">
					{$connectedCategoryId = $selectedPlugin->getCategoryIdByImportId($pluginCategoryId)}
					<option value=""{if !$connectedCategoryId} selected="selected"{/if}>
						{translations name='importcategories.no_category'}
					</option>
					{foreach $categories as $category}
						<option value="{$category->id}"{if $connectedCategoryId == $category->id} selected="selected"{/if}>
							{$category->title}
						</option>
					{/foreach}
				</select>
			</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="2">
			<h1 class="form_inner_title">
				{translations name='accimportplugin.groups'}:
			</h1>
		</td>
	</tr>
	{foreach $selectedPlugin->getGroups() as $group}
		{$pluginCategoryId = $group->GroupId}
		<tr>
			<td class="form_label">
				{$group->GroupName}:
			</td>
			<td colspan='2'>
				<select class="dropdown_placeholder" name="{$formNames.categoriesInput}[{$pluginCategoryId}]">
					{$connectedCategoryId = $selectedPlugin->getCategoryIdByImportId($pluginCategoryId)}
					<option value=""{if !$connectedCategoryId} selected="selected"{/if}>
						{translations name='importcategories.no_category'}
					</option>
					{foreach $categories as $category}
						<option value="{$category->id}"{if $connectedCategoryId == $category->id} selected="selected"{/if}>
							{$category->title}
						</option>
					{/foreach}
				</select>
			</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="2">
			<h1 class="form_inner_title">
				{translations name='accimportplugin.classes'}:
			</h1>
		</td>
	</tr>
	{foreach $selectedPlugin->getClasses() as $class}
		{$pluginCategoryId = $class->ClassId}
		<tr>
			<td class="form_label">
				{$class->ClassName}:
			</td>
			<td colspan='2'>
				<select class="dropdown_placeholder" name="{$formNames.categoriesInput}[{$pluginCategoryId}]">
					{$connectedCategoryId = $selectedPlugin->getCategoryIdByImportId($pluginCategoryId)}
					<option value=""{if !$connectedCategoryId} selected="selected"{/if}>
						{translations name='importcategories.no_category'}
					</option>
					{foreach $categories as $category}
						<option value="{$category->id}"{if $connectedCategoryId == $category->id} selected="selected"{/if}>
							{$category->title}
						</option>
					{/foreach}
				</select>
			</td>
		</tr>
	{/foreach}
</table>