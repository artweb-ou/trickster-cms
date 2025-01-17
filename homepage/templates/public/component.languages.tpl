{if count($languagesList)>1}
{stripdomspaces}
	<div class='languages_block'>
		{foreach $languagesList as $language}
			<a class='language_item{if $language->requested} language_active{/if}' href='{$languageLinks[$language->iso6393]}' title="{$language->title}">
				{$language->title}
			</a>
		{/foreach}
	</div>
{/stripdomspaces}
{/if}