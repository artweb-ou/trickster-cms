{if isset($allowedTypes)}
	{include file=$theme->template("block.newelement.tpl") allowedTypes=$allowedTypes}
{/if}

{if isset($rootPrivileges.deleteElements)}
	<button type="button" class="actions_form_button button warning_button actions_form_delete" data-url="" data-action="deleteElements" data-confirmation="{translations name="message.deleteselectedconfirm"}"><span class="icon icon_delete"></span>{translations name="button.deleteselected"}</button>
{/if}
{if isset($rootPrivileges.copyElements)}
	<button type="button" class="actions_form_button button actions_form_copy" data-url="{$rootElement->URL}" data-action="copyElements">{translations name="button.copyselected"}</button>
{/if}
{if isset($rootPrivileges.cloneElements)}
	<button type="button" class="actions_form_button button actions_form_clone" data-url="" data-action="cloneElements">{translations name="button.cloneselected"}</button>
{/if}
{if isset($currentElementPrivileges.xlsExport)}
	<a class="button" href="{$element->getExportLink()}">{translations name="catalogue.exportxlsx"}</a>
{/if}
