{if $menuLevel}
	<ul class="treemenu_level treemenu_level_{$level}">
	{foreach from=$menuLevel item=treeItem}
		<li class='treemenu_item{if $treeItem->final} treemenu_selected_item{elseif $treeItem->requested && $requests>$level} treemenu_requested_item{/if}'>
			<a href="{$treeItem->URL}" class='structure_element' title="element {$treeItem->id}">
				<span class="icon icon_{if $treeItem->structureType == 'root' && $treeItem->marker == 'admin_root'}website{else}{$treeItem->structureType}{/if}"></span>
				<span class="treemenu_item_title">{$treeItem->getTitle()}</span>
			</a>
			{if $treeItem->requested}{$requests = ($requests +1)*$level}
				{include file=$theme->template("block.tree.tpl") menuLevel=$treeItem->getChildrenList("container") level=$level+1 requests = $requests}
			{/if}
		</li>
	{/foreach}
	</ul>
{/if}