<?xml version="1.0" encoding="utf-8"?>

<rss version="2.0">
	<channel>
		<title>{translations name="rss.channel_title" required=false}</title>
		<link>{$controller->baseURL}</link>
		<description></description>
		<language></language>
		<ttl>60</ttl>
		{foreach from=$rssItems item=rssItem}
			{include file=$theme->template($rssItem->getTemplate()) element=$rssItem}
		{/foreach}
	</channel>
</rss>