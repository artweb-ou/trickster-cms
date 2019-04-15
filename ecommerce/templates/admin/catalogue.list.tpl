{$requestArguments=$element->getRequestArguments()}
{assign var='formNames' value=$element->getFormNames()}
{assign var='formData' value=$element->getFormData()}
{if $element->structureType == 'catalogue'}
<div class="filtration_component">
	{include file=$theme->template('catalogue.filter.tpl')}
	{include file=$theme->template('catalogue.massedit_form.tpl')}
</div>
{/if}
{assign var='formNames' value=$rootElement->getFormNames()}
<div class="content_list_block">
<form action="{$currentElement->getFormActionURL()}" class="content_list_form" method="post" enctype="multipart/form-data">

	<div class='controls_block content_list_controls'>
		<input type="hidden" class="content_list_form_id" value="{$rootElement->id}" name="id" />
		<input type="hidden" class="content_list_form_action" value="" name="action" />

		{if $currentElement->structureType == 'category'}
			{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->catalogueElement->getAllowedTypes() newElementUrl=$currentElement->catalogueElementURL}
		{else}
			{include file=$theme->template('block.buttons.tpl') allowedTypes=$currentElement->getAllowedTypes()}
		{/if}
	</div>
{stripdomspaces}
{if $productsList}
	<table class='content_list'>
	<thead>
		<tr>
			<th class='checkbox_column'>
				<input class='groupbox checkbox_placeholder' type="checkbox" value='1' />
			</th>
			<th class='image_column'>
				{translations name='label.image'}
			</th>
			<th class="name_column">
				<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('title')}">
					{translations name='label.name'}
				</a>
				{if $requestArguments.order && $requestArguments.order.field == 'title'}
					<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>
				{/if}
			</th>

			<th class='code_column'>
				<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('code')}">
					{translations name='field.code'}
				</a>
				{if $requestArguments.order && $requestArguments.order.field == 'code'}
					<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>
				{/if}
			</th>
			<th class='price_column'>
				<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('price')}">
					{translations name='field.price'}
				</a>
				{if $requestArguments.order && $requestArguments.order.field == 'price'}
					<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>
				{/if}
			</th>
			<th>
				<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('availability')}">
					{translations name='productslist.availability'}
				</a>
				{if $requestArguments.order && $requestArguments.order.field == 'availability'}
					<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>
				{/if}
			</th>
			{*<th>*}
				{*<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('showincategory')}">*}
					{*{translations name='productslist.displayinshort'}*}
				{*</a>*}
				{*{if $requestArguments.order && $requestArguments.order.field == 'showincategory'}*}
					{*<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>*}
				{*{/if}*}
			{*</th>*}
			<th class='category_column'>
				{translations name='label.category'}
			</th>
			<th class='edit_column'>
				{translations name='label.edit'}
			</th>
			<th class='date_column'>
				<a class="content_list_field_orderable" href="{$element->getContentListOrderUrl('dateModified')}">
					{translations name='label.date'}
				</a>
				{if $requestArguments.order && $requestArguments.order.field == 'dateModified'}
					<span class="content_list_field_order_indicator content_list_field_order_indicator_{$requestArguments.order.argument}"></span>
				{/if}
			</th>
			<th class='delete_column'>
				{translations name='label.delete'}
			</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$productsList item=contentItem}
		{if $contentItem->structureType != 'positions'}
		{assign var='typeName' value=$contentItem->structureType}
		{assign var='typeLowered' value=$contentItem->structureType|strtolower}
		{assign var='type' value="element."|cat:$typeLowered}
		{assign var='privilege' value=$privileges.$typeName}
		<tr class="content_list_item elementid_{$contentItem->id}">
			<td class="checkbox_cell">
				<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.elements}[{$contentItem->id}]" value="1" />
			</td>
			<td class='image_column'>
				{if !empty($contentItem->getFirstImageUrl())}
					<img src='{$contentItem->getFirstImageUrl()}' />
				{/if}
			</td>
			<td class='name_column'>
				<a href="{$contentItem->URL}">
					<span class='icon icon_{$contentItem->structureType}'></span>{$contentItem->getTitle()}
				</a>
			</td>


			<td class='code_column'>
				{$contentItem->code}
			</td>
			<td class='price_column'>
				{$contentItem->price}
			</td>
			<td class=''>
				{assign var='type' value="product."|cat:$contentItem->availability}
				{translations name=$type}
				{*{if $contentItem->availability == 'available'}*}
					{*{translations name='label.available'}*}
				{*{elseif $contentItem->availability == 'quantity_dependent'}*}
					{*{translations name='label.quantity_dependent'}*}
				{*{elseif $contentItem->availability == 'inquirable'}*}
					{*{translations name='label.inquirable'}*}
				{*{elseif $contentItem->availability == 'unavailable'}*}
					{*{translations name='label.unavailable'}*}
				{*{/if}*}
			</td>
			<td class='category_column'>
				{foreach $contentItem->getConnectedAdminCategories() as $category}
					<a href="{$category->URL}">{$category->getTitle()}</a>{if !$category@last}, {/if}
				{/foreach}
			</td>
			{*<td class=''>*}
				{*{if $contentItem->showincategory == '1'}<span class="content_list_asterisk">&#10004;</span>{/if}*}
			{*</td>*}
			<td class="edit_column">
				{if $privilege.showForm}
					<a href="{$contentItem->URL}id:{$contentItem->id}/action:showForm" class='icon icon_edit'></a>
				{/if}
			</td>
			{*<td class='type_column'>*}
				{*{translations name=$type}*}
			{*</td>*}

			<td>
				{$contentItem->dateModified}
			</td>
			<td class="delete_column">
				{if $privilege.delete}
					<a href="{$contentItem->URL}id:{$contentItem->id}/action:delete" class='icon icon_delete content_item_delete_button'></a>
				{/if}
			</td>
		</tr>
		{/if}
	{/foreach}
	</tbody>
	</table>
	<div class="content_list_bottom">
		{include file=$theme->template("pager.tpl")}
	</div>
{/if}
{/stripdomspaces}
</form>
</div>