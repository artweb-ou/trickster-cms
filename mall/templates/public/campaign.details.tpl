{if $element->title}
	{capture assign="moduleTitle"}{$element->title}{/capture}
{/if}
{capture assign="moduleContent"}
	{if $element->originalName != ""}
		<div class="campaign_details_image_wrap">
			<img class="campaign_details_image" src='{$controller->baseURL}image/type:campaignDetailsImage/id:{$element->image}/filename:{$element->originalName}' alt="{$element->title}"/>
		</div>
	{/if}
	<div class="campaign_details_main">
		<div class="campaign_details_description html_content">
			{$element->content|default:$element->introduction}
		</div>
	</div>
	{if $element->shopURL}
		<div class="campaign_details_controls">
			<a href="{$element->shopURL}" class='campaign_details_readmore button'>
				<span class='button_left'></span>
				<span class='button_right'></span>
				<span class='button_center'></span>
				<span class='button_text'>{translations name='campaign.shoplink'}</span>
			</a>
		</div>
	{/if}
	<div class="clearfix"></div>
{/capture}
{assign moduleClass "campaign_details"}
{assign moduleTitleClass "campaign_details_title"}
{assign moduleContentClass "campaign_details_content"}
{include file=$theme->template("component.contentmodule.tpl")}