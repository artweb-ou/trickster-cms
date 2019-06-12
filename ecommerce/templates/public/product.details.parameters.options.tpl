{if !empty($parameterInfo)}
	{if $parameterInfo.structureType == 'productParameter'}
		{$parameterInfo.value}
	{elseif $parameterInfo.structureType == 'productSelection'}
		{foreach from=$parameterInfo.productOptions item=option name=options}
			{if $option.originalName}
				<img class="product_parameter_icons_item lazy_image" src="{$theme->getImageUrl('lazy.png')}" data-lazysrc="{$controller->baseURL}image/type:productOption/id:{$option.image}/filename:{$option.originalName}" alt="{$option.title}" title="{$option.title}" />
			{else}
				{$option.title}{if !$smarty.foreach.options.last},&#32;{/if}
			{/if}
		{/foreach}
	{/if}
{/if}