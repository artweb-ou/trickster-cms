{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{if $element->content != ''}
		<div class='selectedcampaigns_content html_content'>
			{$element->content}
		</div>
	{/if}
	{if $campaigns = $element->getCampaignsToDisplay()}
		{if $element->layout == 'detailed'}
			{foreach $campaigns as $campaign}
				{include file=$theme->template('campaign.short.tpl') element=$campaign}
			{/foreach}
		{elseif $element->layout == 'scrolling'}
				{stripdomspaces}
				{foreach $campaigns as $campaign}
					{include file=$theme->template('campaign.thumbnail.tpl') element=$campaign}

				{/foreach}
			{if count($campaigns) > 4}
					<div class="scrollitems_previous"></div>
					<div class="scrollitems_next"></div>
			{/if}
				{/stripdomspaces}
		{else}{stripdomspaces}
		{foreach $campaigns as $campaign}
			{include file=$theme->template('campaign.thumbnail.tpl') element=$campaign}
		{/foreach}
		{/stripdomspaces}{/if}
	{/if}
{/capture}

{assign moduleClass "selectedcampaigns_block"}
{assign moduleTitleClass "selectedcampaigns_heading"}

{if $element->layout != 'scrolling'}
	{assign moduleContentClass "selectedcampaigns_content"}
{else}
	{assign moduleContentClass "selectedcampaigns_content scrollitems"}
{/if}

{include file=$theme->template("component.contentmodule.tpl")}