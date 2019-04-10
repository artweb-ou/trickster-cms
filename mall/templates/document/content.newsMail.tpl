{if isset($data.content)}
<div class="newsmail_content">
	{$data.content}
</div>
{/if}
{foreach $data.subContents as $subContentCategoryCode => $subContentInfos}
	{if $contentTheme->templateExists("subContent.{$subContentCategoryCode}.tpl")}
		{include file=$contentTheme->template("subContent.{$subContentCategoryCode}.tpl") subContentInfos=$subContentInfos}
	{/if}
{/foreach}