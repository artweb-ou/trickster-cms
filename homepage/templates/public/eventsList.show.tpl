{if $h1 = $element->getH1()}
	{capture assign="moduleTitle"}
		{$h1}
	{/capture}
{elseif $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}

{$listLayout = $element->getCurrentLayout('listLayout')}
{$currentLayout = $element->getCurrentLayout()}

{if $currentLayout == 'listaggregated'}
	{$groupedEventsIndex = $element->getGroupedEvents('month')}
{elseif $currentLayout == 'calendar'}
	{$groupedEventsIndex = $element->getGroupedEvents('list')}
{else}
	{$groupedEventsIndex = $element->getGroupedEvents($listLayout)}
{/if}
{$monthsInfo = $element->getMonthsInfo()}

{capture assign="moduleContent"}
	{include file=$theme->template('eventsList.filter.tpl')}

	{if $groupedEventsIndex}
		{if $currentLayout == 'calendar'}
			{include file=$theme->template("eventsList.$currentLayout.tpl")}
		{else}
			{if $listLayout == 'month'}
				<div class="eventlist_months">
					{foreach $groupedEventsIndex as $groupedEvents}
						{$monthInfo = $monthsInfo.{$groupedEvents@key}}
						<div class="eventslist_month">
							<h2 class="eventslist_month_title">
								{translations name='calendar.month_'|cat:$monthInfo.month} {$monthInfo.year}
							</h2>
							<div class="eventslist_detailed_month_events">
								<div class="eventlist_list">
									{include file=$theme->template("eventsList.$currentLayout.tpl") dateInfo=$monthInfo events=$groupedEvents}
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			{elseif $listLayout == 'list'}
				<div class="eventlist_list">
					{include file=$theme->template("eventsList.$currentLayout.tpl") dateInfo=$monthsInfo events=$groupedEventsIndex list=true}
				</div>
			{/if}
		{/if}


	{/if}
{/capture}

{assign moduleClass "eventslist eventslist_$currentLayout"}
{assign moduleTitleClass "eventslist_title"}
{assign moduleContentClass "eventslist_content"}

{include file=$theme->template("component.contentmodule.tpl")}
