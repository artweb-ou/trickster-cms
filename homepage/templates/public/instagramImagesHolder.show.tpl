{capture assign="moduleTitle"}
	{$element->getTitle()}
{/capture}
{capture assign="moduleContent"}
	{if $images = $element->getImages()}
		<div class="instagram_holder_images">
			{foreach $images as $image}
				<a class="instagram_holder_images">
					<img src="{$image->image}" />
				</a>
			{/foreach}
		</div>
	{/if}
{/capture}
{assign moduleClass "instagram_holder"}
{assign moduleTitleClass "instagram_holder_heading"}
{assign moduleContentClass "instagram_holder_content"}
{include file=$theme->template("component.contentmodule.tpl")}