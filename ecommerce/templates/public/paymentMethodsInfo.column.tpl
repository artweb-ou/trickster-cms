{capture assign="moduleContent"}
	{if $element->link != ''}
		<a href="{$element->link}" class="paymentmethodsinfo_block">
			<span class="paymentmethodsinfo_title">{$element->title}</span>
			<span class="paymentmethodsinfo_bottom"></span>
		</a>
	{else}
		<span class="paymentmethodsinfo_block">
			<span class="paymentmethodsinfo_title">{$element->title}</span>
			<span class="paymentmethodsinfo_bottom"></span>
		</span>
	{/if}
{/capture}

{assign moduleClass "payment_methods_info_block"}
{include file=$theme->template("component.columnmodule.tpl")}