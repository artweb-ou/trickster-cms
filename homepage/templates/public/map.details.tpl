{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<div class='map_content'>
		<div class='map_content_text html_content'>
			{$element->content}
		</div>
		{if $element->getFullAddress()}
			<p class="map_address">
				{$element->getFullAddress()}
			</p>
		{/if}
	</div>
	{if $element->mapCode}
		<div class="map_map map_embedded">
			{$element->mapCode}
		</div>
	{elseif $element->coordinates}
		<div class="map_map googlemap_id_{$element->id}">
			<script>
				/*<![CDATA[*/
				window.mapsInfo = window.mapsInfo || {ldelim}{rdelim};
				window.mapsInfo['{$element->id}'] = {$element->getJsonMapInfo()};
				/*]]>*/
			</script>
		</div>
	{/if}
{/capture}

{assign moduleClass "map map_details map_id_{$element->id}"}
{assign moduleTitleClass "map_title"}
{include file=$theme->template("component.contentmodule.tpl")}
