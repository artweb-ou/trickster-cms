<a href="{$discount->URL}" class="discount_column">
	<span class="discount_column_top">
		{if $element->originalName != ''}
			<span class="discount_column_image">
				{include file=$theme->template('component.elementimage.tpl') type='discountColumn' lazy=true}
			</span>
		{/if}
		<span class="discount_column_title">{$element->title}</span>
		<span class="discount_column_dates">
			{if $element->startDate && $element->endDate}
				{$element->startDate} - {$element->endDate}
			{elseif $element->startDate}
				{translations name="discount.startdate"} {$element->startDate}
			{elseif $element->endDate}
				{translations name="discount.enddate"} {$element->endDate}
			{/if}
		</span>
	</span>

</a>