<title>{if !empty($currentMetaTitle)}{$currentMetaTitle}{/if}</title>
{if !empty($currentMetaKeywords)}<meta name="keywords" content="{$currentMetaKeywords}" />{/if}
{if !empty($currentMetaDescription)}<meta name="description" content="{$currentMetaDescription}" />{/if}
{if !empty($currentNoIndexing)}<meta name="robots" content="noindex" />{/if}
{if !empty($currentCanonicalUrl)}<link rel="canonical" href="{$currentCanonicalUrl}"/>{/if}