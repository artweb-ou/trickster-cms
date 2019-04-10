<div class="products_filter">

	{$searchArguments=$element->parseSearchArguments()}

	{if $element->isFilterableByCategory()}
		{foreach $element->getFiltersByType('category') as $filter}
			{if $filter->isRelevant()}
				{include $theme->template('component.filterdropdown.tpl')}
			{/if}
		{/foreach}
	{/if}

	{if $element->isFilterableByParameter()}
		{foreach $element->getFiltersByType('parameter') as $filter}
			{if $filter->isRelevant() && $filter->canBeDisplayedInCategory()}
				{$selection = $filter->getSelectionElement()}
				{if $selection && $selection->controlType == 'radios'}
					{include $theme->template('component.filterRadio.tpl')}
				{else}
					{include $theme->template('component.filterdropdown.tpl')}
				{/if}
			{/if}
		{/foreach}
	{/if}


	{if $element->isFilterableByBrand()}
		{foreach $element->getFiltersByType('brand') as $filter}
			{if $filter->isRelevant()}
				{include $theme->template('component.filterdropdown.tpl')}
			{/if}
		{/foreach}
	{/if}

	{if $element->isFilterableByDiscount()}
		{foreach $element->getFiltersByType('discount') as $filter}
			{if $filter->isRelevant()}
				{include $theme->template('component.filterdropdown.tpl')}
			{/if}
		{/foreach}
	{/if}

	{if $element->isFilterableByAvailability()}
		{foreach $element->getFiltersByType('availability') as $filter}
			{if $filter->isRelevant()}
				{include $theme->template('component.filterdropdown.tpl')}
			{/if}
		{/foreach}
	{/if}

	{if $element->isFilterableByPrice()}
		{foreach $element->getFiltersByType('price') as $filter}
			{$filter->setRangeInterval($element->priceInterval)}
			{if $filter->isRelevant()}
				{include $theme->template('component.filterdropdown.tpl')}
			{/if}
		{/foreach}
	{/if}
</div>