<table class="socialpost_publish_table">
	{foreach from=$element->getPublishingInfo() item=info}
		<tr>
			<td class="socialpost_publish_title">{$info.title}</td>
			<td class="socialpost_publish_controls">
				<form action="{$info.publishURL}" method="GET">
					<input type="hidden" value="{$element->id}" name="socialPostId" />
					<input type="hidden" value="{$element->URL}id:{$element->id}/action:showPublishing/" name="return" />
					{if $info.pages}
						{foreach $info.pages as $page}
							<div>
								{$status = $page->statusText}
								{translations name="socialpost.status_$status"}
								<label>
									<input type="checkbox" name="pages[]" value="{$page->socialId}" />
									<span>{$page->title}</span>
								</label>
							</div>
						{/foreach}
					{else}
						<div>
							{assign statusText $info.statusText}
							{if $statusText}
								{translations name="socialpost.status_$statusText"}
							{/if}
							<label>
								<input type="checkbox" name="pages[]" value="0" />
								<span></span>
							</label>
						</div>
					{/if}

					<input type="submit" value="{translations name="socialpost.publish"}" />
				</form>
			</td>
		</tr>
	{/foreach}
</table>