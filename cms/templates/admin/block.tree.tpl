{if $menuLevel}
	<ul class="treemenu_level treemenu_level_{$level}">
	{foreach from=$menuLevel item=treeItem}
		<li class='treemenu_item{if $treeItem->final} treemenu_selected_item{/if}'>
			<a href="{$treeItem->URL}" class='structure_element' title="element {$treeItem->id}">
				<span class="icon icon_{if $treeItem->structureType == 'root' && $treeItem->marker == 'admin_root'}website{else}{$treeItem->structureType}{/if}"></span>
				<span class="treemenu_item_title">{$treeItem->getTitle()}</span>
			</a>
			{if $treeItem->requested}
				{include file=$theme->template("block.tree.tpl") menuLevel=$treeItem->getChildrenList("container") level=$level+1}
			{/if}
		</li>
	{/foreach}
	</ul>
{/if}