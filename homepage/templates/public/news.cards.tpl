{*{assign moduleTitle $element->title}*}
{assign moduleTag "div"}
{capture assign="moduleContent"}

	{*{if !empty($element->avatar)}*}
		{*{assign moduleAvatar}*}
	{*{/if}*}
	{if !empty($settings.default_owner_name)}
		{assign moduleOwnername $settings.default_owner_name}
	{/if}
	{if $moduleOwnername}
		{$moduleOwnername}
	{/if}

	<div class='news_short_content dateTime'>
		{$element->date|date_format:"%e. %B %Y"}
	</div>

	{if $element->originalName}
		{include file=$theme->template('component.elementimage.tpl') type='newsShortImage' class='news_short_image' lazy=true}
		{*{capture assign="moduleSideContent"}*}
		{*{/capture}*}
	{/if}
	<div class='news_short_content html_content'>
		{$element->introduction}
	</div>

{/capture}
{if $element->content}
	{capture assign="moduleControls"}
		<a href="{$element->URL}" class='news_short_readmore button'>
			<span class='button_text'>{translations name='news.news_short_readmore'}</span>
		</a>
	{/capture}
{/if}

{assign moduleClass "news_short"}
{assign moduleTitleClass "news_short_title"}
{assign moduleSideContentClass "news_short_image_block"}
{include file=$theme->template("component.subcontentmodule_square.tpl") moduleTitle=false}