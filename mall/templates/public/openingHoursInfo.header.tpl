{if $groups = $element->getGroups()}
	<div class="header_openinghours">
		<div class="header_linehack"></div>
		<div class="header_openinghours_content">
			{foreach $groups as $group}
				<div class="header_openinghours_group">
					<span class="header_openinghours_group_title">{$group->title}:</span>
					<span class="header_openinghours_group_hours">{if $groupInfo = $group->getOpeningHoursInfo()}
						{foreach $groupInfo as $groupInfoPeriod}
							{$groupInfoPeriod.name} {$groupInfoPeriod.times}{if !$groupInfoPeriod@last}, {/if}
						{/foreach}
					{/if}</span>
				</div>
			{/foreach}
			{if $element->exceptional}
				<a class="header_openinghours_exceptions" href="{$element->URL}">
					{translations name='openinghours_info.exceptions'}
				</a>
			{/if}
		</div>
	</div>
{/if}