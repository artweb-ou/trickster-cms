{if empty($height)}{$height = 500}{/if}
{if empty($galleryResizeType)}{$galleryResizeType = 'imagesHeight'}{/if}
<div class="product_details_gallery gallery_slide galleryid_{$element->id}">
	{foreach from=$element->getImagesList() item=image}
		{include file=$theme->template($image->getTemplate()) element=$image}
	{/foreach}
	<script>

		window.galleriesInfo = window.galleriesInfo || {ldelim}{rdelim};
		window.galleriesInfo['{$element->id}'] = {$element->getGalleryJsonInfo([
		'thumbnailsSelectorEnabled'=>true,
		'fullScreenGalleryEnabled'=>true,
		'galleryResizeType'=>{$galleryResizeType},
		'height'=>{$height},
		'imageResizeType'=>'resize',
		'descriptionType'=>'hidden'
		], 'productGallery', 'desktop')};

	</script>

	<script>

		window.urlList = [];
		{foreach from=$element->getImagesList() item=image name=gallery}
		{if $image->originalName != ""}
		urlList['full_image{$image->id}'] = '{$controller->baseURL}image/type:galleryFullImage/id:{$image->image}/filename:{$image->originalName}';
		{/if}
		{/foreach}

	</script>
</div>