<table class="socialpost_publish_table">
	{foreach from=$element->getPublishingInfo() item=info}
		<tr>
			{assign var=statusText value=$info.statusText}
			<td class="socialpost_publish_title">{$info.title}</td>
			<td class="socialpost_publish_controls">
				<form action="{$info.publishURL}" method="GET">
					<input type="hidden" value="{$element->id}" name="socialPostId" />
					<input type="hidden" value="{$element->URL}id:{$element->id}/action:showPublishing/" name="return" />
					{foreach $info.pages as $page}
						{$status = $page->statusText}
						{translations name="socialpost.status_$status"}
						<label>
							<input type="checkbox" name="pages[]" value="{$page->id}" />
							<span>{$page->title}</span>
						</label><br />
					{/foreach}

					<input type="submit" value="{translations name="socialpost.publish"}" />
				</form>
			</td>
		</tr>
	{/foreach}
</table>