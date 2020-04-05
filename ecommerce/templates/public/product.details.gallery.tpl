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
</div>