{assign var='newsList' value=$element->getNewsList(true)}
{if $newsList}
	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	{capture assign="moduleContent"}
		{stripdomspaces}
			<div class="latestnews_news news_list">
				{foreach $newsList as $news}
					{include file=$theme->template($news->getTemplate($element->getCurrentLayout())) element=$news}
				{/foreach}
			</div>
		{if $pager = $element->getPager()}
			{include file=$theme->template('pager.tpl')}
		{/if}
		{/stripdomspaces}
	{/capture}

	{assign moduleClass "latestnews latestnews_layout_{$element->getCurrentLayout()}"}
	{assign moduleContentClass "latestnews_content"}
	{assign moduleTitleClass "latestnews_title"}
	{include file=$theme->template("component.contentmodule.tpl")}
{/if}