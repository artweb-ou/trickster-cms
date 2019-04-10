{capture assign="moduleTitle"}{if $element->searchTitle}{$element->searchTitle}{/if}{/capture}
{capture assign="moduleSideContent"}
	{if $element->originalName}
		<img class="campaign_search_image" src='{$controller->baseURL}image/type:campaignSearch/id:{$element->image}/filename:{$element->originalName}' alt="{$element->title}"/>
	{/if}
{/capture}
{capture assign="moduleContent"}
	<div class='search_result_content'>
		{$element->searchContent|default:$element->introduction}
	</div>
	<div class="search_result_controls">
		<a class="search_result_button button" href='{$element->URL}'>
			<span class='button_text'>
				{translations name='search.readmore'}
			</span>
		</a>
	</div>
{/capture}

{assign moduleClass "campaign_search clickable_component"}
{assign moduleTitleClass "search_result_content_title"}
{assign moduleAttributes ""}
{assign moduleSideContentClass ""}
{include file=$theme->template("component.subcontentmodule_wide.tpl")}