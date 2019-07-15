{stripdomspaces}
{if !empty($contentOnly)}{if isset($moduleContent)}
	<section class="contentmodule_content{if isset($moduleContentClass)} {$moduleContentClass}{/if}">{$moduleContent}</section>
{/if}{else}
	<section class="contentmodule_component{if isset($moduleClass)} {$moduleClass}{/if}" {if isset($moduleAttributes)}{$moduleAttributes}{/if}>
		{if isset($moduleTitle)}
			<h1 class="contentmodule_title{if isset($moduleTitleClass)} {$moduleTitleClass}{/if}"{if isset($moduleTitleAttributes)} {$moduleTitleAttributes}{/if}>{$moduleTitle}</h1>{/if}
		{if isset($moduleContent)}
			<div class="contentmodule_content{if isset($moduleContentClass)} {$moduleContentClass}{/if}">{$moduleContent}</div>{/if}
	</section>
{/if}
{/stripdomspaces}