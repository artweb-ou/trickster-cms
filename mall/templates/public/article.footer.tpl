<section class="footer_article_item">
	<div class="footer_article_item_title">
		{$element->title}
	</div>
	{if $element->content != ""}
		<div class="article_content html_content">
				{$element->content}
		</div>
	{/if}
</section>