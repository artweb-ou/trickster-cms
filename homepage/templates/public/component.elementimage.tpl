{stripdomspaces}
{if empty($src)}
	{$src = $element->generateImageUrl($type)}
{/if}
{if !isset($title)}
	{if $element->title}
		{$title = $element->title}
	{else}
		{$title = ''}
	{/if}
{/if}
{if empty($class)}
	{$class = ''}
{/if}
{if empty($lazy)}
	<img class="{$class}" src="{$src}" alt="{$title}"/>
{else}
	<img class="{$class} lazy_image" src="" data-lazysrc="{$src}" alt="{$title}"/>
{/if}
{/stripdomspaces}