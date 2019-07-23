{function printMenus level=1 items=[]}
	<ul class="sub_menu_list_mobile_menu_item sub_menu_list_mobile_menu_item_level_{$level}" role="menu">
	{foreach $items as $subMenu}
			{if $subMenu->title}
				<li>
					<a href="{$subMenu->URL}" class="sub_menu_list_mobile_menu_link{if $subMenu->requested} sub_menu_list_mobile_menu_active{/if} menuid_{$subMenu->id}" role="menuitem">
						<span class="sub_menu_list_mobile_menu_text">{$subMenu->title}</span>
					</a>
				</li>
			{/if}
			{if $level < $element->levels || ($subMenu->requested && $level < $element->maxLevels)}
				{if $subMenu->getSubMenuList()}
					{call name=printMenus level=$level+1 items=$subMenu->getSubMenuList()}
				{/if}
			{/if}
	{/foreach}
	</ul>
{/function}
{if $element->getSubMenuList()}
	<div class="mobilemenu_module mobilemenu_module_submenulist">
		{if $element->displayHeadingAutomatically && empty($noTitle)}
		{if $element->title}
			<div class="mobilemenu_module_title">
				{$element->title}
			</div>
		{/if}
		{/if}
		<div class="mobilemenu_module_content">
			{call name=printMenus items=$element->getSubMenuList()}
		</div>
	</div>
{/if}