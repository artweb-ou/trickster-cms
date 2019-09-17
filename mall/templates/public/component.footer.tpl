<footer class="footer" role="contentinfo">
	<div class="footer_main">{stripdomspaces}
		{if $element = $currentLanguage->getElementFromFooter('newsMailForm')}
			<div class="footer_line">
				{include file=$theme->template('newsMailForm.footer.tpl')}
			</div>
		{/if}
		{if $articles = $currentLanguage->getElementsFromFooter('article')}
			{if count($articles) > 1}
				{foreach $articles as $article}
					<div class="footer_module">
						<div>
							{include file=$theme->template('article.footer.tpl') element=$article}
						</div>
					</div>
				{/foreach}
			{else}
				{if $widget = $currentLanguage->getElementFromFooter('widget')}
					<div class="footer_module">
						<div>
							{include file=$theme->template('article.footer.tpl') element=$articles[0]}
						</div>
					</div>
					<div class="footer_module">
						<div>
							{include file=$theme->template('widget.footer.tpl') element=$widget}
						</div>
					</div>
				{else}
					<div class="footer_module">
						{include file=$theme->template('article.footer.tpl') element=$articles[0]}
					</div>
				{/if}
			{/if}
		{elseif $widget = $currentLanguage->getElementFromFooter('widget')}
			<div class="footer_line">
				{include file=$theme->template('widget.footer.tpl') element=$widget}
			</div>
		{/if}
		{/stripdomspaces}
	</div>
	{stripdomspaces}
		{if $map = $currentLanguage->getElementFromFooter('map')}
			<div class="footer_map">
				{include file=$theme->template('map.footer.tpl') element=$map}
			</div>
		{/if}
		<div class="footer_links">
{*			{include file=$theme->template("component.mainmenu.tpl")}*}
			{include file=$theme->template("component.artweb.tpl")}
			<div class="clearfix"></div>
		</div>
	{/stripdomspaces}
</footer>