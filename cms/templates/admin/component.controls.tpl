{if !isset($action)}
	{assign 'action' 'receive'}
{/if}
<div class="controls_block form_controls">
	<input type="hidden" value="{$element->id}" name="id" />
	<input type="hidden" value="{$action}" name="action" />
	{foreach $form->getControls() as $key=>$control}
		<button class="button {$control.class}"
			{if !empty($control.type)} type="{$control.type}" {else} type="button"{/if}
			{if !empty($control.action)} control-action="{$control.action}"{/if}>
			{translations name="button.{$key}"}
			{if !empty($control.icon)}<span class="icon icon_{$control.icon}"></span>{/if}
		</button>
	{/foreach}
</div>