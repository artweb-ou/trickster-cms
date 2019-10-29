{if !empty($item.translationGroup)}
	{$translationGroup = $item.translationGroup}
{else}
	{$translationGroup = $structureType}
{/if}
{assign addClass ""}
{if !empty($item.class)}
	{$addClass = '_'|cat:$item.class}
{/if}
<div class="form_items form_items{$addClass}">
	<div class="form_label"></div>
	<div class="form_label heading">
		<h2 class="content_list_title">
			{translations name="{$translationGroup}.{$fieldName}"}
		</h2>
	</div>
</div>