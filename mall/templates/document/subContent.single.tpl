{foreach $subContentInfos as $subContentInfo}
	<div class="newsmail_subcontent_item newsmail_subcontent_single">
		<table class="newsmail_subcontent_item_table">
			<tr>
				{if $subContentInfo.image}
					<td class="newsmail_subcontent_item_table_left"><img class="newsmail_subcontent_item_image" src="{$controller->baseURL}image/type:newsMailSubcontentSingle/id:{$subContentInfo.image}/filename:{$subContentInfo.image}" /></td>
				{/if}
				<td class="newsmail_subcontent_item_table_right">
					<h2 class="newsmail_subcontent_item_title">{$subContentInfo.title}</h2>
					{if $subContentInfo.content}<div class="newsmail_subcontent_item_content">{$subContentInfo.content}</div>{/if}
					{if $subContentInfo.link}
						<img src="{$dispatchmentType->getImageUrl('arrow_little.png')}" alt="" />&nbsp;
						<a href="{$subContentInfo.link}" class="newsmail_subcontent_item_link">{$subContentInfo.linkName}</a>
					{/if}
				</td>
			</tr>
		</table>
	</div>
{/foreach}