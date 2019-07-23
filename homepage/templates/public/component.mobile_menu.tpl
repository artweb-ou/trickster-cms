{stripdomspaces}
	<div class="mobilemenu">
		<div class="mobilemenu_main">
			<div class="mobilemenu_closeicon"></div>
			{include file=$theme->template('component.languages_selector_in_row.tpl')}
			{foreach $currentLanguage->getMobileMenuElementsList() as $element}
				{include file=$theme->template($element->getTemplate("mobileMenu")) element=$element}
			{/foreach}
		</div>
	</div>
{/stripdomspaces}