{if count($languagesList) > 1}
	<div class="languages_selector">
		<select class="languages_selector_select redirect_select">
			{foreach $languagesList as $language}
				<option value='{$language->URL}'{if $language->requested} selected="selected"{/if}>{$language->title}</option>
			{/foreach}
		</select>
		<div class="languages_selector_label">{$currentLanguage->title}</div>
	</div>
{/if}