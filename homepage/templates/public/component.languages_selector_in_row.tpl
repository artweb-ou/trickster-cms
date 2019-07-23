{if count($languagesList) > 1}
	<div class="languages_selection">
		<div class="languages_items redirect_select">
			{foreach $languagesList as $language}
				<span class="languages_item_container">
					<a href="{$controller->baseURL}redirect/type:language/element:{$currentElement->id}/code:{$language->iso6393}/" class="languages_item_link{if $language->requested} active{/if}">
						<span class='languages_item'>{$language->title}</span>
					</a>
				</span>
			{/foreach}
		</div>
	</div>
{/if}