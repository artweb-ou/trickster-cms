{stripdomspaces}
	<div class="mobilemenu">
		<div class="mobilemenu_main">
			{include file=$theme->template('component.languages.tpl')}
			{foreach $currentLanguage->getMobileMenuElementsList() as $element}
				{include file=$theme->template($element->getTemplate("mobileMenu")) element=$element}
			{/foreach}
			<div class="mobilemenu_closeicon"></div>
		</div>
	</div>
{/stripdomspaces}