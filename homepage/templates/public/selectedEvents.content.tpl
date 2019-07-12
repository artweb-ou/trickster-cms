{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{include file=$theme->template("component.eventslist.tpl")}
	{if $eventsLists = $element->getConnectedEventsLists()}
		<div class="button_link_container calendar_link_container text_center">
			{foreach $eventsLists as $eventsList}
				<a href="{$eventsList->URL}" class="button_link bordered"><span class="button_link_text">{translations name='events.look_calendar'}</span></a>
			{/foreach}
		</div>
	{/if}
{/capture}

{assign colorLayoutStyle ''}
{if $element->getCurrentLayout('colorLayout')}
	{$colorLayoutStyle = "colorlayout_bg_color_{$element->getCurrentLayout('colorLayout')}"}
	{assign moduleAttributes "data-color='colorlayout_bg_color'"}
{/if}

{assign moduleClass "selectedevents selectedevents_$currentLayout $colorLayoutStyle"}
{assign moduleTitleClass "selectedevents_title selectedevents_title_$currentLayout"}
{assign moduleContentClass "selectedevents_content"}

{include file=$theme->template("component.contentmodule.tpl")}