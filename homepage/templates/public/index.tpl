<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	{include file=$theme->template("index.head.meta.tpl")}
	{include file=$theme->template("index.head.seo.tpl")}
	{include file=$theme->template("index.head.opengraph.tpl")}
	{include file=$theme->template("index.head.twitter.tpl")}
	{include file=$theme->template("index.head.facebook.tpl")}
	<link rel="shortcut icon" href="{$controller->baseURL}favicon.ico" />
	<link rel="stylesheet" type="text/css" href="{$controller->baseURL}css/set:{$theme->getCode()}/{if !empty($CSSFileName)}file:{$CSSFileName}.css{/if}" />
	{if !empty($jsScripts)}{foreach $jsScripts as $script}<script defer type="text/javascript" src="{$script}"></script>{/foreach}{/if}
	<link rel="alternate" type="application/rss+xml" href="{$controller->baseURL}rss/{$currentLanguage->iso6393}/" title="RSS" />
	{foreach $languagesList as $language}
		{if !$language->requested}
			<link rel="alternate" hreflang="{$language->iso6392}" href="{$controller->baseURL}redirect/type:language/element:{$currentElement->id}/code:{$language->iso6393}/" />
		{/if}
	{/foreach}
	{if $currentElement instanceof LdJsonProviderInterface}{$currentElement->getLdJsonScriptHtml()}{/if}
	{if $currentLanguage->hidden}
		<meta name="robots" content="noindex">
	{/if}
</head>
<body class="{if $currentLanguage->patternBackground} language_pattern_background{/if}{if $firstPageElement->requested}homepage{/if}" {if $currentLanguage->backgroundImage} style="background-image:url('{$controller->baseURL}image/type:background/id:{$currentLanguage->backgroundImage}/filename:{$currentLanguage->backgroundImageOriginalName}');{if $currentLanguage->patternBackground}background-repeat: repeat; background-size: auto{/if}"{/if}>

{include file=$theme->template("component.mainblock.tpl")}
{include file=$theme->template("javascript.data.tpl")}
{$googleAD = $configManager->get('google.ad')}
{if !empty($googleAD.ad_enabled) && $googleAD.ad_enabled}
	{include file=$theme->template("javascript.googlead.tpl")}
{/if}
</body>
</html>