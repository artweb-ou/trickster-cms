<div class="map_footer">
	{if $element->title}<div class="map_footer_title">{$element->title}</div>{/if}
	{if $element->mapCode}
	<div class="map_map map_embedded">
			{$element->mapCode}
		</div>
	{elseif $element->coordinates}
	<div class="map_map googlemap_id_{$element->id}">
		{if !empty($element->styles|trim)}
			<script>
				/*<![CDATA[*/
				window.mapsInfo = window.mapsInfo || {ldelim}{rdelim};
				window.mapsInfo['{$element->id}'] = {ldelim}
					'coordinates': '{$element->coordinates}',
					'title': '{$element->title}',
					'content': '{$element->description}',
					'zoomControlEnabled': true,
					'streetViewControlEnabled': false,
					'mapTypeControlEnabled': false,
					{if $element->styles}'styles': {$element->styles},{/if}
					'heightAdjusted': 'true',
					'height': 0.185
				{rdelim};
				/*]]>*/
			</script>
		{else}
		<div class="gmap_iframe">
				<iframe src = "https://maps.google.com/maps?q={$element->coordinates}&z=14&amp;output=embed"></iframe>
		</div>
		{/if}
		</div>
	{/if}
</div>