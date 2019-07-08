{if count($languagesList) > 1}
	<div class="languages_selector">
		<select class="languages_selector_select redirect_select">
			{foreach $languagesList as $language}
				<option value='{$controller->baseURL}redirect/type:language/element:{$currentElement->id}/code:{$language->iso6393}/'{if $language->requested} selected="selected"{/if}>{$language->title}</option>
			{/foreach}
		</select>
		<div class="languages_selector_label">{$currentLanguage->title}</div>
	</div>
{/if}