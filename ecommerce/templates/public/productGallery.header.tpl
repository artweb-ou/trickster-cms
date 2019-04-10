{if !isset($height)}
	{$height = 0.5}
{/if}
<div class="productgallery productgallery_header productgallery_id_{$element->id}">
	<div class="productgallery_images_container">
		<script type="text/javascript">
			{if $galleryInfo = $element->getGalleryInfo([
				'popupPositioning'=>'mark',
				 'staticDescriptionEnabled'=>true,
				 'imageDescriptionEnabled'=>false,
				 'heightLogics'=>'imagesAspected',
				 'height'=>{$height}
				 ])}
			window.productGalleriesInfo = window.productGalleriesInfo || {ldelim}{rdelim};
			window.productGalleriesInfo[{$element->id}] = {$galleryInfo};
			{/if}
		</script>
	</div>
</div>