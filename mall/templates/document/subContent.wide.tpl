{foreach $subContentInfos as $subContentInfo}
	<div class="newsmail_subcontent_item newsmail_subcontent_wide">
		<table class="newsmail_subcontent_item_table">
			{if $subContentInfo.image}
				<tr>
					<td class="newsmail_subcontent_item_image_wrapper">
						<img class="newsmail_subcontent_item_image" src="{$controller->baseURL}image/type:newsMailSubcontentWide/id:{$subContentInfo.image}/filename:{$subContentInfo.image}" />
					</td>
				</tr>
			{/if}
			<tr>
				<td>
					<h2 class="newsmail_subcontent_item_title">{$subContentInfo.title}</h2>
					{if $subContentInfo.content}
						<div class="newsmail_subcontent_item_content">{$subContentInfo.content}</div>{/if}
					{if $subContentInfo.link}
						<img src="{$dispatchmentType->getImageUrl('arrow_little.png')}" alt="" />&nbsp;
						<a href="{$subContentInfo.link}" class="newsmail_subcontent_item_link">{$subContentInfo.linkName}</a>
					{/if}
				</td>
			</tr>
		</table>
	</div>
{/foreach}