<script>
	/*<![CDATA[*/
	window.menusInfo = [];
	{foreach from=$currentLanguage->getMainMenuElements() item=menu}
		{if !$menu->hidden}
			var menuInfo = {ldelim}{rdelim};
			menuInfo['title'] = '{$menu->title}';
			menuInfo['parentId'] = '0';
			menuInfo['id'] = '{$menu->id}';
			menuInfo['URL'] = '{$menu->URL}';
			window.menusInfo.push(menuInfo);
			{*{foreach from=$menu->getSubMenuList() item=subMenu}*}
				{*{if !$subMenu->hidden}*}
					{*var menuInfo = {ldelim}{rdelim};*}
					{*menuInfo['title'] = '{$subMenu->title}';*}
					{*menuInfo['parentId'] = '{$menu->id}';*}
					{*menuInfo['id'] = '{$subMenu->id}';*}
					{*menuInfo['URL'] = '{$subMenu->URL}';*}
					{*window.menusInfo.push(menuInfo);*}
				{*{/if}*}
			{*{/foreach}*}
		{/if}
	{/foreach}
	/*]]>*/
</script>