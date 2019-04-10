{if $h1 = $element->getH1()}
	{capture assign="moduleTitle"}
		{$h1}
	{/capture}
{elseif $element->title}
	{capture assign='moduleTitle'}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{if $element->content!=''}
	<div class='gallery_details_content html_content'>
		{$element->content}
	</div>
	{/if}

	{if count($element->images)}
	{stripdomspaces}
		{if $element->layout == "slide"}
			<div class="gallery_details_images gallery_{$element->getSlideType()} galleryid_{$element->id}">
				{foreach from=$element->images item=image name=gallery}
					{include file=$theme->template($image->getTemplate()) element=$image captionLayout=$element->captionLayout columnWidth=$element->getColumnWidth()}
				{/foreach}
			</div>
		{elseif $element->layout == "autoheight"}
			<div class="gallery_details_images gallery_static galleryid_{$element->id}">
				{$imagesInRow=0}
				{foreach $element->images as $image}
					{if $imagesInRow == 0}<div class="blocks_autoheight_component">{/if}
					{include file=$theme->template($image->getTemplate()) element=$image captionLayout=$element->captionLayout thumbnailPreset="galleryThumbnailUnevenImage"}
					{$imagesInRow = $imagesInRow + 1}
					{if $imagesInRow == $element->getColumns() || $image@last}</div>{$imagesInRow=0}{/if}
				{/foreach}
			</div>
		{else}
			<div class="gallery_details_images gallery_static galleryid_{$element->id}">
				{foreach from=$element->images item=image name=gallery}
					{include file=$theme->template($image->getTemplate()) element=$image captionLayout=$element->captionLayout columnWidth=$element->getColumnWidth()}
				{/foreach}
			</div>
		{/if}
	{/stripdomspaces}
	{/if}

	<script>
		/*<![CDATA[*/
		window.galleriesInfo = window.galleriesInfo || {ldelim}{rdelim};
		window.galleriesInfo['{$element->id}'] = {$element->getGalleryJsonInfo([
			'galleryResizeType' => 'aspected',
			'galleryHeight' => 0.5625,
			'thumbnailsSelectorEnabled' => true
		], 'gallery', $deviceType)};
		/*]]>*/
	</script>

	{if $element->serviceElement}
	<div class="gallery_details_controls">
		<a href="{$element->serviceElement->URL}" class="button gallery_details_service">
			<span class='button_text'>{$element->serviceElement->title}</span>
		</a>
	</div>
	{/if}
{/capture}

{assign moduleClass "gallery_details"}
{assign moduleTitleClass "gallery_details_heading"}

{include file=$theme->template("component.contentmodule.tpl")}