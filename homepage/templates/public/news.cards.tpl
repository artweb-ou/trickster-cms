{*{assign moduleTitle $element->title}*}
{assign moduleTag "div"}

{if $currentElement->socMedia_1_Name && $currentElement->socMedia_1_Icon}
	{$sharedUrl = "{$currentElement->getUrlEncoded($currentElement->URL)}"}
	{if $currentElement->generalOwnerName}
		{$generalOwnerName =  $currentElement->getUrlEncoded($currentElement->generalOwnerName|cat:'. ')}
	{/if}
	{if $currentElement->title}
		{$currentTitle = $currentElement->getUrlEncoded($currentElement->title)}
	{/if}
	{$sharedName = "{if $generalOwnerName}{$generalOwnerName}{/if}{if $currentTitle}{$currentTitle}{/if}"}

{*
	//'Facebook',
	//'Twitter',
	//'Google+',
	//'LinkedIn',
*}

	{if $currentElement->socMedia_1_Name == 'fb'}
		{$href = "https://www.facebook.com/sharer/sharer.php?u={$sharedUrl}&t={$sharedName}"}
		{$shareTitle = 'Facebook'}
		{$smTarget = 'facebook'}
	{elseif $currentElement->socMedia_1_Name == 'tw'}
		{$href = "https://twitter.com/share?url={$sharedUrl}&text={$sharedName}"}
		{$shareTitle = 'Twitter'}
		{$smTarget = 'twitter'}
	{elseif $currentElement->socMedia_1_Name == 'gl'}
		{$href = "https://plus.google.com/share?url={$sharedUrl}"}
		{$shareTitle = 'Google+'}
		{$smTarget = 'google'}
	{elseif $currentElement->socMedia_1_Name == 'li'}
		{$href = "https://www.linkedin.com/shareArticle?mini=true&url={$sharedUrl}&title={$sharedName}&source={$generalOwnerName}"}
		{$shareTitle = 'LinkedIn'}
		{$smTarget = 'linkedin'}
	{/if}
	{$socMedia_1_Icon = "{$controller->baseURL}image/type:newsItemIcon/id:{$currentElement->socMedia_1_Icon}/filename:{$currentElement->socMedia_1_IconOriginalName}"}

	{capture assign="socMedia_1"}
		<a href="{$href}" class="sm_share {$smTarget}" data-sm-target="{$smTarget}" title="{translations name='news.share_on'} {$shareTitle}">
			{include file=$theme->template('component.elementimage.tpl') class='news_icon' src=$socMedia_1_Icon}
		</a>
	{/capture}

{/if}

{capture assign="moduleContent"}
{*
{$currentElement->cols}
{$currentElement->captionLayout}
*}


	{if $currentElement->generalOwnerAvatar}
		{assign var="iconAvatar" value="{$controller->baseURL}image/type:newsItemIcon/id:{$currentElement->generalOwnerAvatar}/filename:{$currentElement->generalOwnerAvatarOriginalName}"}
		<span class="news_top news_ownwer_avatar">
			{include file=$theme->template('component.elementimage.tpl') type='newsItemIcon' class='news_icon avatar' src=$iconAvatar lazy=true}
		</span>
	{/if}

	{if $currentElement->generalOwnerName}
		<span class="news_top news_ownwer_name">{$currentElement->generalOwnerName}</span>
	{/if}

	{if $socMedia_1}
		<span class="news_top news_sm">
		{$socMedia_1}
		</span>
	{/if}

	<div class='news_top news_date'>
		{$element->date|date_format:"%e. %B %Y"}
	</div>

	{if $element->introduction}
		<div class='news_short_content html_content'>
			{$element->introduction}
		</div>
	{/if}

	{if $element->originalName}
		{include file=$theme->template('component.elementimage.tpl') type='newsShortImage' class='news_short_image' lazy=true}
	{/if}
	{*{capture assign="moduleSideContent"}*}
	{*{/capture}*}

{/capture}
{if $element->content}
	{capture assign="moduleControls"}
		<a href="{$element->URL}" class='news_short_readmore button'>
			<span class='button_text'>{translations name='news.news_short_readmore'}</span>
		</a>
	{/capture}
{/if}

{assign moduleClass "news_card"}
{assign moduleTitleClass "news_card_title"}
{assign moduleSideContentClass "news_short_image_block"}
{include file=$theme->template("component.subcontentmodule_set_cols.tpl") moduleTitle=false colsOnRow={$currentElement->cols}}