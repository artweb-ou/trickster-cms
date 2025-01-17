{if !empty($languageLinks)}
    {foreach $languageLinks as $code=>$languageLink}
        {if $code !== $currentLanguage->iso6393}
            <link rel="alternate" hreflang="{$code}" href="{$languageLink}"/>
        {/if}
    {/foreach}
{/if}