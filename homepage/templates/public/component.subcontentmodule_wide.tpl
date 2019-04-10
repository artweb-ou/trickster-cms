<section class="subcontentmodule_component subcontentmodule_wide{if !empty($moduleClass)} {$moduleClass}{/if}" {if !empty($moduleAttributes)}{$moduleAttributes}{/if}>
	{if !empty($moduleSideContent)}<div class="subcontentmodule_side{if !empty($moduleSideContentClass)} {$moduleSideContentClass}{/if}">{$moduleSideContent}</div>{/if}
	<div class="subcontentmodule_center">
		<div class="subcontentmodule_center_top">
			{if !empty($moduleTitle)}<h2 class="subcontentmodule_title{if !empty($moduleTitleClass)} {$moduleTitleClass}{/if}"{if !empty($moduleTitleAttributes)} {$moduleTitleAttributes}{/if}>{$moduleTitle}</h2>{/if}
			{if !empty($moduleContent)}<div class="subcontentmodule_content{if !empty($moduleContentClass)} {$moduleContentClass}{/if}">{$moduleContent}</div>{/if}
		</div>
		{if !empty($moduleControls)}
			<div class="subcontentmodule_wide_controls subcontentmodule_controls{if !empty($moduleControlsClass)} {$moduleControlsClass}{/if}">
				{if !empty($moduleSideContent)}
					<div class="subcontentmodule_wide_controls_side"></div>
				{/if}
				<div class="subcontentmodule_wide_controls_content">{$moduleControls}</div>
			</div>
		{/if}
	</div>
</section>