{if $element->title}
	{capture assign="moduleTitle"}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	<div class="comments_list_controls">
		{include file=$theme->template("pager.tpl") pager=$element->getPager()}
	</div>
	<div class='comments_list gallery_pictures' id="gallery_{$element->id}">
		{foreach from=$element->getCommentsList() item=comment}
			{include file=$theme->template("comment.full.tpl") element=$comment displaySubComments=false}
		{/foreach}
	</div>
	<div class="comments_list_controls">
		{include file=$theme->template("pager.tpl") pager=$element->getPager()}
	</div>
{/capture}
{assign moduleClass ""}
{assign moduleTitleClass ""}
{assign moduleContentClass ""}

{include file=$theme->template("component.contentmodule.tpl")}
