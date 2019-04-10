{* don't show if the element has not been created yet *}
{if $element->id|strpos:"showForm" === false}
	{include file=$theme->template("catalogue.list.tpl") productsList=$element->getAdminProductsList() pager=$element->getProductsPager()}
{/if}