{stripdomspaces}
{foreach $subMenus as $subMenu}
	<div class="submenu_item submenu_item_level_{$level}{if $subMenu->requested} submenu_item_active{/if}">
		<a href="{$subMenu->URL}" class="submenu_item_link{if !empty($usePopup) && ($levels == $level)}{if $subMenu->getSubMenuList()} submenu_item_haspopup{/if}{/if} menuid_{$subMenu->id}{if !empty($verticalPopup)} vertical_popup{/if}">
			<span class="submenu_item_icon"></span>
			<span class="submenu_item_text" role="menuitem">{$subMenu->title}</span>
		</a>
		{if $level < $levels || ($subMenu->requested && $level < $element->maxLevels)}
			{if $subMenu->getSubMenuList()}
			<div class="submenu_item_submenus" role="menu">
				{include file=$theme->template("subMenuList.items.tpl") level=$level+1 subMenus=$subMenu->getSubMenuList()}
			</div>
			{/if}
		{/if}
	</div>
{/foreach}
{/stripdomspaces}