{if $element->title}
	{capture assign="moduleTitle"}{$element->title}{/capture}
{/if}
{capture assign="moduleContent"}
	{foreach $element->getGroups() as $group}
		<div class="openinghours_details_group">
			{if $exceptions = $group->getExceptions()}
				<h2 class="openinghours_details_group_title">
					{$group->title}
				</h2>
				<div class="openinghours_details_exceptions">
					{foreach $exceptions as $exception}
						<div class="openinghours_details_exception">
							<span class="openinghours_details_exception_title">
								{$exception->title}
							</span>
							<span class="openinghours_details_exception_info">
								{if $exception->closed}
									<span class="openinghours_details_exception_date">{$exception->startDate}{if $exception->endDate} - {$exception->endDate}{/if}</span> {translations name='openinghours_exception.closed'}
								{else}
									<span class="openinghours_details_exception_date">{$exception->startDate}{if $exception->endDate} - {$exception->endDate}{/if}</span> {$exception->startTime}-{$exception->endTime}
								{/if}
							</span>
						</div>
					{/foreach}
				</div>
			{/if}
		</div>
	{/foreach}
{/capture}
{assign moduleClass "openinghours_details"}
{assign moduleTitleClass "openinghours_details_heading"}
{assign moduleContentClass "openinghours_details_content html_content"}

{include file=$theme->template("component.contentmodule.tpl")}