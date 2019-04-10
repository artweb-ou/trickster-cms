{if $element->link}
	<a class='banner' style="{if $element->width}width: {$element->width}px; {/if}{if $element->height}height: {$element->height}px;{/if}" href="{$controller->baseURL}banner/id:{$element->id}"{if $element->openInNewWindow} target="_blank"{/if}>
{else}
	<span style="{if $element->width}width: {$element->width}px; {/if}{if $element->height}height: {$element->height}px;{/if}" class="banner">
{/if}
{if $element->originalName}
	<img class="lazy_image" style="{if $element->width}width: {$element->width}px; {/if}{if $element->height}height: {$element->height}px;{/if}" alt="" src="{$theme->getImageUrl('lazy.png')}" data-lazysrc="{$controller->baseURL}file/mode:view/id:{$element->image}/filename:{$element->originalName}" />
	<span class='banner_cover'></span>
{/if}
{if $element->link}
	</a>
{else}
	</span>
{/if}