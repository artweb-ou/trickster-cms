<div class="sales_statistics_content sales_statistics_filter_component">
	{assign var='formNames' value=$element->getFormNames()}
	<div class="filtration_component">
		<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data">
			<div class="sales_statistics_inputs">
				<div class="sales_statistics_filter_controls">
					<div class="sales_statistics_date_range panel_component">
						<div class="catalogue_filter_control_label catalogue_filter_title panel_heading">
							{translations name='sales_statistics.date_range'}
						</div>
						<div class="panel_content">
							<div class="sales_statistics_date_presets">
								<a class="sales_statistics_date" data-start="{date("d.m.Y", strtotime("-7 days"))}" data-end="{date("d.m.Y")}">
									{translations name='sales_statistics.last7days'}
								</a>
								<a class="sales_statistics_date" data-start="{date("d.m.Y", strtotime("-1 month"))}" data-end="{date("d.m.Y")}">
									{translations name='sales_statistics.last30days'}
								</a>
								<a class="sales_statistics_date" data-start="{date("d.m.Y", strtotime("first day of this month"))}" data-end="{date("d.m.Y")}">
									{translations name='sales_statistics.current_month'}
								</a>
								<a class="sales_statistics_date" data-start="{date("d.m.Y", strtotime("first day of previous month"))}" data-end="{date("d.m.Y", strtotime("last day of previous month"))}">
									{translations name='sales_statistics.last_month'}
								</a>
								<a class="sales_statistics_date" data-start="{date("d.m.Y", strtotime("first day of -6 month"))}" data-end="{date("d.m.Y")}">
									{translations name='sales_statistics.last6months'}
								</a>
							</div>
						</div>
						<div class="panel_control">
							<input type="hidden" value="{$element->id}" name="id" />
							<input type="hidden" value="showTurnover" name="action" />
							<input class="input_component" style="display: none;" />

							<div class="catalogue_filter_control">
								<div class="catalogue_filter_control_date">
									<input class="input_component sales_statistics_filter_start input_date" type="text" value="{$element->getFilterParameter('start')}" name="{$formNames.start}" />
									<input class="input_component sales_statistics_filter_end input_date" type="text" value="{$element->getFilterParameter('end')}" name="{$formNames.end}" />
								</div>
							</div>
						</div>
					</div>
					<div class="display_like_statistics panel_component">
						<div class="catalogue_filter_title form_inner_title panel_heading">{translations name='sales_statistics.display_like'}</div>
						<div class="radio_tabs_component sales_statistics_display_types panel_content">
							{stripdomspaces}
								<label class="button">{translations name='sales_statistics.display_products_sum'}
									<input type="radio" name="{$formNames.display}" value="productsSum" {if $element->getFilterParameter('display') == 'productsSum'}checked="checked"{/if} /></label>
								<label class="button">{translations name='sales_statistics.display_product_count'}
									<input type="radio" name="{$formNames.display}" value="productCount" {if $element->getFilterParameter('display') == 'productCount'}checked="checked"{/if} /></label>
								<label class="button">{translations name='sales_statistics.display_amount'}
									<input type="radio" name="{$formNames.display}" value="amount" {if $element->getFilterParameter('display') == 'amount' || !$element->getFilterParameter('display')}checked="checked"{/if} /></label>
								<label class="button">{translations name='sales_statistics.display_avg_product_count'}
									<input type="radio" name="{$formNames.display}" value="avgProductsCount" {if $element->getFilterParameter('display') == 'avgProductsCount'}checked="checked"{/if} /></label>
								<label class="button">{translations name='sales_statistics.display_avg_sum'}
									<input type="radio" name="{$formNames.display}" value="avgSum" {if $element->getFilterParameter('display') == 'avgSum'}checked="checked"{/if} /></label>
								<label class="button">{translations name='sales_statistics.display_order_count'}
									<input type="radio" name="{$formNames.display}" value="orderCount" {if $element->getFilterParameter('display') == 'orderCount'}checked="checked"{/if} /></label>
							{/stripdomspaces}
						</div>
					</div>
					<div class="search_block_statistics panel_component">
						<div class="catalogue_filter_control">
							<div class="catalogue_filter_control_label catalogue_filter_title">{translations name='sales_statistics.category'}</div>
							<div class="catalogue_filter_control_box ">
								<select class="sales_statistics_filter_category_block" name={$formNames.category} autocomplete='off'>
									{if $categoryId = $element->getFilterParameter('category')}
									<option value="{$categoryId}" name='' selected="selected">
										{$element->getElementTitle($categoryId)}
									</option>
									{/if}
								</select>
							</div>
						</div>
						<div class="catalogue_filter_control">
							<div class="catalogue_filter_control_label">{translations name='sales_statistics.product'}</div>
							<div class="catalogue_filter_control_box">
								<select class="sales_statistics_filter_product_block" name={$formNames.product} autocomplete='off'>
									{if $productId = $element->getFilterParameter('product')}
									<option value="{$productId}" name='' selected="selected">
										{$element->getElementTitle($productId)}
									</option>
									{/if}
								</select>
							</div>
						</div>
						<div class="catalogue_filter_control">
							<div class="catalogue_filter_control_label">{translations name='sales_statistics.user_group'}</div>
							<div class="catalogue_filter_control_box">
								<select class="sales_statistics_filter_user_group_block" name="{$formNames.user_group}" autocomplete='off'>
									{if $userGroupId = $element->getFilterParameter('user_group')}
										<option value="{$userGroupId}" selected="selected">
											{$element->getElementTitle($userGroupId, 'description')}
										</option>
									{/if}
								</select>
							</div>
						</div>
					</div>
					<div class="sales_statistics_groupby panel_component">
						<div>
							<div class="catalogue_filter_title form_inner_title panel_heading">{translations name='sales_statistics.group_by'}</div>
							<div class="radio_tabs_component sales_statistics_periods panel_content">
								{stripdomspaces}
									<label class="button">{translations name='sales_statistics.day'}
										<input type="radio" name="{$formNames.group}" value="day" {if $element->getFilterParameter('group') == 'day' || !$element->getFilterParameter('group')}checked="checked"{/if} /></label>
									<label class="button">{translations name='sales_statistics.week'}
										<input type="radio" name="{$formNames.group}" value="week" {if $element->getFilterParameter('group') == 'week'}checked="checked"{/if} /></label>
									<label class="button">{translations name='sales_statistics.month'}
										<input type="radio" name="{$formNames.group}" value="month" {if $element->getFilterParameter('group') == 'month'}checked="checked"{/if} /></label>
									<label class="button">{translations name='sales_statistics.year'}
										<input type="radio" name="{$formNames.group}" value="year" {if $element->getFilterParameter('group') == 'year'}checked="checked"{/if} /></label>
								{/stripdomspaces}
							</div>
						</div>
						<div>
							<div class="catalogue_filter_title form_inner_title panel_heading">{translations name='sales_statistics.listtype'}</div>
							<div class="radio_tabs_component sales_statistics_list_types panel_content">
								{stripdomspaces}
									<label class="button">{translations name='sales_statistics.list_order'}
										<input type="radio" name="{$formNames.list}" value="order" {if $element->getFilterParameter('list') == 'order' || !$element->getFilterParameter('list')}checked="checked"{/if} /></label>
									<label class="button">{translations name='sales_statistics.list_product'}
										<input type="radio" name="{$formNames.list}" value="product" {if $element->getFilterParameter('list') == 'product'}checked="checked"{/if} /></label>
								{/stripdomspaces}
							</div>
						</div>
					</div>
				</div>
				<div class="catalogue_filter_buttons form_controls">
					<button class="button primary_button" type="submit">
						<span>{translations name='sales_statistics.filter_button'}</span>
					</button>
				</div>
			</div>
		</form>
	</div>
	<div class="sales_statistics_info">
		{if $element->displayBarChart()}
			<div class="panel_component">
				<div class="panel_content">
					<div class="sales_statistics_categories_chart">
						<canvas class="chart_component" data-chartid="2" data-charttype="bar"{if $element->displayCurrencyInChart()} data-currency="{$symbol}"{/if}></canvas>
					</div>
				</div>
			</div>
			<div class="sales_statistics_legend panel_component">
				<div class="panel_content">
					<table class="table_component">
						<thead>
						<tr>
							<th class="sales_statistics_color"></th>
							<th>{translations name='sales_statistics.label_category_name'}</th>
							<th class="sales_statistics_value">{translations name='sales_statistics.label_quantity'}</th>
							<th class="sales_statistics_value">{translations name='sales_statistics.label_total'}</th>
							<th class="sales_statistics_value">{translations name='sales_statistics.label_percent'}</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td class="sales_statistics_color">

							</td>
							<td>
								{translations name='sales_statistics.total'}
							</td>
							<td class="sales_statistics_value">
								{$element->getProductsTotalQuantity()}
							</td>
							<td class="sales_statistics_value">
								{sprintf('%01.2f', $productsTotal)} {$symbol}
							</td>
							<td class="sales_statistics_value">
								100%
							</td>
						</tr>
						{foreach $element->getCategoriesLegendItems() as $category}
							{if $categoryStyle = $element->getCategoryStyle($categoryId)}
								<tr>
									<td class="sales_statistics_color">
										<div style="background: {$category.borderColor};">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
									</td>
									<td>
										{$category.label}
									</td>
									<td class="sales_statistics_value">
										{$category.productsCount}
									</td>
									<td class="sales_statistics_value">
										{sprintf('%01.2f', $category.productsTotal)} {$symbol}
									</td>
									<td class="sales_statistics_value">
										{{$category.percent}|round:2}%
									</td>
								</tr>
							{/if}
						{/foreach}
						{if $element->emptyCategoryExists()}
							{if $categoryStyle = $element->getCategoryStyle(0)}
								<tr>
									<td class="sales_statistics_color">
										<div style="background: {$categoryStyle.borderColor};">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
									</td>
									<td>
										{$categoryStyle.label}
									</td>
									<td class="sales_statistics_value">
										{$element->getProductsCountForUndefinedCategory()}
									</td>
									<td class="sales_statistics_value">
										{$total = $element->getProductsTotalForUndefinedCategory()}
										{sprintf('%01.2f', $total)} {$symbol}
									</td>
									<td class="sales_statistics_value">
										{($total / $productsTotal * 100)|round:2}%
									</td>
								</tr>
							{/if}
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		{/if}
		<div class="sales_statistics_chart panel_component">
			<div class="sales_statistics_chart_content panel_content">
				<canvas class="chart_component container_height_depends panel_content" data-chartid="1"{if $element->displayCurrencyInChart()} data-currency="{$symbol}"{/if}></canvas>
				<script>
					window.chartsData = {$chartData};
				</script>
			</div>
		</div>
	</div>
	<div class="content_list_block">

		{if isset($pager)}
			{include file=$theme->template("pager.tpl") pager=$pager}
		{/if}

		{if $listType == 'product'}
			{include file=$theme->template('salesStatistics.productsList.tpl') contentList=$listElements}
		{else}
			{include file=$theme->template('salesStatistics.ordersList.tpl') contentList=$listElements}
		{/if}
		{if isset($pager) && $listElements}
			<div class="content_list_bottom">
				{include file=$theme->template("pager.tpl") pager=$pager}
			</div>
		{/if}
	</div>
</div>
