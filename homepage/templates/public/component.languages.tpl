{if count($languagesList)>1}
{stripdomspaces}
	<div class='languages_block'>
		{foreach $languagesList as $language}
			<a class='language_item{if $language->requested} language_active{/if}' href='{$controller->baseURL}redirect/type:language/element:{$currentElement->id}/code:{$language->iso6393}/' title="{$language->title}">
				{if $language->originalName != ""}
					<img src='{$controller->baseURL}image/type:languageFlag/id:{$language->image}/filename:{$language->originalName}' alt='{$language->title}' />
				{else}
					{$language->title}
				{/if}
			</a>
		{/foreach}
	</div>
{/stripdomspaces}
{/if}