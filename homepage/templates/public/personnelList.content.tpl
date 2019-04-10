{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{stripdomspaces}
		<div class="personnellist_items">
			{foreach from=$element->getPersonnelList() item=personnel}
				{include file=$theme->template($personnel->getTemplate($element->getCurrentLayout())) element=$personnel}
			{/foreach}
		</div>
	{/stripdomspaces}
{/capture}

{assign moduleClass "personnellist_block"}
{assign moduleTitleClass "personnellist_heading"}
{assign moduleContentClass "personnellist_content"}

{include file=$theme->template("component.contentmodule.tpl")}