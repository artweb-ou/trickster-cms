<!DOCTYPE HTML>
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
	{if method_exists($currentElement, 'getLdJsonScriptHtml')}{$currentElement->getLdJsonScriptHtml()}{/if}
	{if $currentLanguage->hidden}
		<meta name="robots" content="noindex">
	{/if}
</head>
<body class="{if $currentLanguage->patternBackground} language_pattern_background{/if}{if $firstPageElement->requested}homepage{/if}" {if $currentLanguage->backgroundImage} style="background-image:url('{$controller->baseURL}image/type:background/id:{$currentLanguage->backgroundImage}/filename:{$currentLanguage->backgroundImageOriginalName}');{if $currentLanguage->patternBackground}background-repeat: repeat; background-size: auto{/if}"{/if}>

<div class="{if $currentMainMenu->marker} {$currentMainMenu->marker}_element{/if}">
	<div class="wrap">
		{include file=$theme->template('component.mobile_header.tpl')}
		<div class='header'>
			<div class="left_panel_top">
				{stripdomspaces}
				{if $currentLanguage->logoImage}
					<a href='{$firstPageElement->URL}' class="logo_block">
						<div class="header_linehack"></div>
						<img class="logo_image" src="{$controller->baseURL}image/type:logo/id:{$currentLanguage->logoImage}/filename:{$currentLanguage->logoImage}" alt="" />
					</a>
				{/if}
					<div class="header_sociallinks">
						<span class="header_sociallinks_label">{translations name='header.follow_us'}</span>
						<a class="header_sociallinks_item header_sociallinks_item_rss" href="{$controller->baseURL}rss/{$currentLanguage->iso6393}/" title="RSS">
							{file_get_contents($theme->getImagePath("rss.svg"))}
						</a>
						{if !empty($settings.social_link_fb)}
							<a class="header_sociallinks_item header_sociallinks_item_fb" href="{$settings.social_link_fb}" title="Facebook">
								{file_get_contents($theme->getImagePath("fb.svg"))}
							</a>
						{/if}
						{if !empty($settings.social_link_instagram)}
							<a class="header_sociallinks_item header_sociallinks_item_instagram" href="{$settings.social_link_instagram}" title="Instagram">
								{file_get_contents($theme->getImagePath("ins.svg"))}
							</a>
						{/if}
					</div>
					<div class="header_article_block">
						{if $element = $currentLanguage->getElementFromHeader('openingHoursInfo')}
							{include file=$theme->template('openingHoursInfo.header.tpl')}
						{elseif $element = $currentLanguage->getElementFromHeader('article')}
							{include file=$theme->template('article.header.tpl')}
						{/if}
					</div>
				{if $currentLanguage->getElementFromHeader('search')}
					{include file=$theme->template('search.header.tpl') element=$currentLanguage->getElementFromHeader('search')}
				{/if}
				{/stripdomspaces}
			</div>
		</div>
	</div>
	<div class="header_links">
		<div class="header_links_inner wrap">
			{include file=$theme->template("component.mainmenu.tpl")}
			{include file=$theme->template("component.languages.tpl")}
		</div>
	</div>
	<div class="wrap">
		{if $currentLanguage->getElementsFromHeader('gallery')}
			{$headerGallery=$currentLanguage->getElementFromHeader('gallery')}
			{if $headerGallery && $headerGallery->images}
				<div class="header_graphics">
					<div class="header_graphics_inner">
						{$bannerCategory = $currentLanguage->getElementFromHeader('bannerCategory')}
						{if $bannerCategory}
							{$banners = $bannerCategory->getBannersToDisplay()}
							{if $banners}
								<div class="header_gallery_banners">
									{foreach $banners as $banner}
										<div class="header_gallery_banner">
											{include file=$theme->template('banner.show.tpl') element=$banner}
										</div>
										{if $banner@iteration == 2}
											{break}
										{/if}
									{/foreach}
								</div>
							{/if}
						{/if}
						<div class="header_gallery galleryid_{$headerGallery->id} gallery_scroll">
							<script>
								window.galleriesInfo = window.galleriesInfo || {ldelim}{rdelim};
								window.galleriesInfo['{$headerGallery->id}'] = {$headerGallery->getGalleryJsonInfo([
								'descriptionType'=>'hidden',
								'imagesButtonsEnabled'=>true,
								'descriptionType'=>'static',
								'changeDelay'=>6000,
								'thumbnailsSelectorEnabled'=> false,
								'galleryResizeType'=>'aspected',
								'galleryHeight' => 0.2761290322580645,
								'imageResizeType'=>'fit',
								'fullScreenGalleryEnabled'=>false,
								'enablePrevNextImagesButtons'=>true
								], 'headerGallery', 'desktop')};
								</script>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			{/if}
		{/if}
		<div class="content">
			{include file=$theme->template($currentLayout)}
		</div>
		{include file=$theme->template("component.footer.tpl")}
	</div>
</div>
{include file=$theme->template("javascript.data.tpl")}
{$googleAD = $configManager->get('google.ad')}
{if !empty($googleAD.ad_enabled) && $googleAD.ad_enabled}
	{include file=$theme->template("javascript.googlead.tpl")}
{/if}
</body>
</html>