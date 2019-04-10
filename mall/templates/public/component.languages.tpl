{stripdomspaces}
<div class='languages_block'>
	{foreach $languagesList as $language}
			<a class='language_item{if $language->requested} language_active{/if}' href='{$controller->baseURL}redirect/type:language/element:{$currentElement->id}/code:{$language->iso6393}/' title="{$language->title}">
				{$language->title}
			</a>
		{/foreach}
</div>
{/stripdomspaces}