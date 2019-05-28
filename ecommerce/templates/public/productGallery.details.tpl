{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<script>
		{if $galleryInfo = $element->getGalleryInfo(['popupPositioning'=>'mark'])}
			window.productGalleriesInfo = window.productGalleriesInfo || {ldelim}{rdelim};
			window.productGalleriesInfo[{$element->id}] = {$galleryInfo};
		{/if}
	</script>
{/capture}
{assign moduleContentClass "productgallery_images_container"}
{assign moduleClass "productgallery productgallery_id_{$element->id} productgallery_details"}
{include file=$theme->template("component.contentmodule.tpl")}