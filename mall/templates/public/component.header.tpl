<div class='header_block'>
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
            {include file=$theme->template('search.header.tpl') element=$currentLanguage->getElementFromHeader('search') referral='mall'}
        {/if}
        {/stripdomspaces}
    </div>
</div>
<div class="header_links">
    <div class="header_links_inner wrap">
        {if $subMenuList = $currentLanguage->getElementFromHeader('subMenuList')}
            {include file=$theme->template("subMenuList.header.tpl") element=$subMenuList}
        {/if}
        {include file=$theme->template("component.languages.tpl")}
    </div>
</div>
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
