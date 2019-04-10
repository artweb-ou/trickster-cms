{if $element->title}
	{capture assign="moduleTitle"}
		{translations name='search.result_results'}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{stripdomspaces}
	{if $element->result->count}
		{if !$element->result->exactMatches}
			<div class="search_result_message">
				{translations name='search.only_partial_matches'}
			</div>
		{/if}
		{if $pager = $element->getPager()}
			{include file=$theme->template('pager.tpl')}
		{/if}
		{foreach $element->result->sets as $set}
			{if $set->elements}
				{$translationCode = "search.{$set->type}"}
				{if $set->template}
					{include file=$theme->template($set->template) set=$set}
				{else}
					<section class="search_result_set search_result_set_{$set->type}">
						<h2 class='search_result_set_title'>{translations name=$translationCode}</h2>
						<div class="search_result_set_items">
							{foreach $set->elements as $resultElement}
								{if $resultElement}
									{if $theme->templateExists($resultElement->getTemplate($resultElement->getViewName()))}
										{include file=$theme->template($resultElement->getTemplate($resultElement->getViewName())) element=$resultElement}
									{else}
										{include file=$theme->template($resultElement->getTemplate()) element=$resultElement}
									{/if}
								{/if}
							{/foreach}
						</div>
					</section>
				{/if}
			{/if}
		{/foreach}
		{if $pager = $element->getPager()}
			{include file=$theme->template('pager.tpl')}
		{/if}
	{else}
		<div class='search_results html_content'>
			{translations name='search.result_searchnotfound'}
		</div>
	{/if}
	{/stripdomspaces}
{/capture}

{assign moduleTitleClass "search_results_heading"}
{assign moduleClass "search_results_block"}
{assign moduleContentClass "search_results_block"}
{include file=$theme->template("component.contentmodule.tpl")}