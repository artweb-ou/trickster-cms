<?xml version="1.0"?>
<rss version="2.0"
	 xmlns:g="http://base.google.com/ns/1.0">
	<channel>
		<title>Remarketing Feed</title>
		<link>{$controller->baseURL}</link>
		<description>Remarketing Feed</description>
		{foreach $products as $product}
			<item>
				<g:id>{$product->id}</g:id>
				<title>{$product->getHumanReadableName()}</title>
				<link>{$product->getUrl()}</link>
				{if !empty($product->introduction)}
					<description><![CDATA[{$product->introduction}]]></description>
				{/if}
				<g:image_link>{$product->getImageUrl()}</g:image_link>
				{if $imagesList = $product->getImagesList()}
					{foreach $imagesList as $image}
						<g:additional_image_link>
							{$controller->baseURL}image/type:galleryFullImage/id:{$image->image}/filename:{$image->originalName}
						</g:additional_image_link>
					{/foreach}
				{/if}
				<g:price>{$product->getPrice()} {$currencySymbol}</g:price>
				<g:availability>{if $product->isPurchasable()}in stock{else}out of stock{/if}</g:availability >
				{if !empty($product->getBrandElement()->title)}
					<g:brand>{$product->getBrandElement()->title}</g:brand>
				{/if}
				<g:MPN>{$product->code}</g:MPN>
			</item>
		{/foreach}
	</channel>
</rss>