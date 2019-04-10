{*{if $currentLocation = $breadcrumbsManager->getBreadcrumbs()}*}
	{*{if !isset($delimiter)}*}
		{*{$delimiter = '/'}*}
	{*{/if}*}
	{*<div class="breadcrumbs_block">*}
		{*{foreach $currentLocation as $key=>$locationElement}*}
			{*<a class="breadcrumbs_item{if $locationElement@last} breadcrumbs_item_last{/if}" href="{$locationElement.URL}">{$locationElement.title}</a>{if !$locationElement@last} {$delimiter} {/if}*}
		{*{/foreach}*}
	{*</div>*}
{*{/if}*}
{*<script type="application/ld+json">{$breadcrumbsManager->getLdJson()}</script>*}