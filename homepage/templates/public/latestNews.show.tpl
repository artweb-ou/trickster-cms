{assign var='newsList' value=$element->getNewsList(true)}
{if $element->getCurrentLayout('colorLayout')}
	{$colorLayoutStyle = "bg_color_{$element->getCurrentLayout('colorLayout')}"}
	{$moduleAttributes = "data-color='colorlayout_bg_color'"}
{/if}
{if $newsList}
	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	{capture assign="moduleContent"}
		{stripdomspaces}
			<div class="latestnews_news news_list news_{$element->getCurrentLayout()}">
				{foreach $newsList as $news}
					{include file=$theme->template($news->getTemplate($element->getCurrentLayout())) element=$news}
				{/foreach}
			</div>
		{if $pager = $element->getPager()}
			{include file=$theme->template('pager.tpl')}
		{/if}
		{/stripdomspaces}
		{if !empty($element->buttonTitle) && (!empty($element->buttonUrl) || !empty($element->getButtonConnectedMenuUrl()))}
			{if $element->getButtonConnectedMenuUrl()}
				{$Url = $element->getButtonConnectedMenuUrl()}
			{else}
				{$Url = $element->buttonUrl}
			{/if}
			<div class="view_all">
				<a href="{$Url}" class="button view_all_button"><span class="button_text">{$element->buttonTitle}</span></a>
			</div>
		{/if}
	{/capture}

	{if !empty($moduleAttributes )}{assign moduleAttributes $moduleAttributes}{/if}
	{assign moduleClass "latestnews latestnews_layout_{$element->getCurrentLayout()}{if !empty($colorLayoutStyle)} {$colorLayoutStyle}{/if}"}
	{assign moduleContentClass "latestnews_content"}
	{assign moduleTitleClass "latestnews_title latestnews_title_{$element->getCurrentLayout()}"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}