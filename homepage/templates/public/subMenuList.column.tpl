{if $element->getSubMenuList()}
	{capture assign="moduleContent"}
		{if $element->popup}
			<script>
				/*<![CDATA[*/
				window.subMenusInfo = window.subMenusInfo || {ldelim}{rdelim};
				window.subMenusInfo['{$element->id}'] = {$element->getMenusInfo()|json_encode};
				/*]]>*/
			</script>
		{/if}
			<nav class='submenu_items_block'>
                {if $element->displayHeadingAutomatically}
                    {$currentMainMenu->title}
                {else}
                    {if $element->title}
                        {$element->title}
                    {/if}
                {/if}
				{include file=$theme->template("subMenuList.items.tpl") level=1 levels=$element->levels usePopup=$element->popup subMenus=$element->getSubMenuList()}
			</nav>
	{/capture}

	{assign moduleTitleClass "submenu_column_title"}
	{assign moduleClass "submenu_block submenu_column{if $element->layout} submenu_column_{$element->layout}{/if}"}
	{assign moduleContentClass "submenu_column_content submenu_content"}
	{include file=$theme->template("component.columnmodule.tpl")}
{/if}