<item>
	<title><![CDATA[{$element->title}]]></title>
	<link>{$element->URL}</link>
	<description><![CDATA[
	{if $element->originalName != ''}
		<a href='{$element->URL}'>
			<img style="border-style:none;" src='{$controller->baseURL}image/type:rssImage/id:{$element->image}/filename:{$element->originalName}' alt="{$element->title}"/>
		</a>
	{/if}
	{$element->introduction}]]></description>
	<pubDate>{$element->rssDate}</pubDate>
	<guid>{$element->id}</guid>
</item>