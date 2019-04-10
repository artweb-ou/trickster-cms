<div class="header_languageselector">
	{foreach from=$languagesList item=language}
		<a href="{if $currentElement}{$currentElement->URL}{else}{$rootElement->URL}{/if}lang:{$language->iso6393}/" class="header_languageselector_item header_languageselector_item_{$language->iso6393} {if $language->id == $currentLanguageId} header_languageselector_item_current{/if}" title="{$language->title}"></a>
	{/foreach}
</div>