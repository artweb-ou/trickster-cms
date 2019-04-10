

{capture assign="moduleContent"}
	{if $element->content}
		<div class="discountslist_content html_content">
			{$element->content}
		</div>
	{/if}
	<div class="campaignslist_campaigns">
		{foreach $campaigns as $campaign}
			{include file=$theme->template('campaign.short.tpl') element=$campaign}
		{/foreach}
	</div>
{/capture}

{assign moduleClass "campaignslist"}
{assign moduleTitleClass "campaignslist_title"}
{include file=$theme->template("component.contentmodule.tpl")}