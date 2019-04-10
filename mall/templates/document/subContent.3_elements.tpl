<div class="newsmail_subcontent_item newsmail_subcontent_3_elements">
	<table class="newsmail_subcontent_item_table">

		{foreach from=$subContentInfos key=counter item=subContentInfo}

			{if !$counter}
				<tr>
			{elseif !($counter%3)}
				</tr>
				<tr>
			{/if}
				<td class="newsmail_subcontent_item_entry">
					<table>
						{if $subContentInfo.image}
							<tr>
								<td class="newsmail_subcontent_3_elements_image_wrap">
									<img class="newsmail_subcontent_3_elements_image" src="{$controller->baseURL}image/type:newsMailSubcontent3Elements/id:{$subContentInfo.image}/filename:{$subContentInfo.image}" />
								</td>
							</tr>
						{/if}
						<tr>
							<td class="">
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
				</td>
		{/foreach}
			</tr>
	</table>
</div>
