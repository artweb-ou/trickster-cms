<div class="shopcategory_short_header"{if $element->color} style="background-color: #{$element->color}"{/if}>
	{if $element->image}
		<img class="shopcategory_short_icon" src="{$controller->baseURL}image/type:categoryShortIcon/id:{$element->image}/filename:{$element->originalName}" alt="{$element->title}"/>
	{/if}
	<h1 class="shopcategory_short_title">
		{$element->title}
	</h1>
</div>