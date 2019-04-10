{if !empty($contentOnly)}{if isset($moduleContent)}
	<div class="contentmodule_content{if isset($moduleContentClass)} {$moduleContentClass}{/if}">{$moduleContent}</div>{/if}{else}
	<div class="columnmodule_component{if isset($moduleClass)} {$moduleClass}{/if}">
	{if isset($moduleTitle)}
		<div class="columnmodule_title{if isset($moduleTitleClass)} {$moduleTitleClass}{/if}">{$moduleTitle}</div>{/if}
	{if isset($moduleContent)}
		<div class="columnmodule_content{if isset($moduleContentClass)} {$moduleContentClass}{/if}">{$moduleContent}</div>{/if}
	</div>
{/if}