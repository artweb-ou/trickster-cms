{if isset($item.method)}
	{assign var='options' value=$form->callElementMethod($item.method)}
	{*{var_dump($options)}*}
{elseif isset($item.property)}
	{assign var='options' value=$form->getElementProperty($item.property)}
{elseif isset($item.select_options)}
	{assign var='options' value=$form->getElementOptions($item.select_options)}
	{if !empty($item.translationGroup)}
		{assign var='translated' value = $item.translationGroup}
	{/if}
{else}
	{assign var='options' value=""}
{/if}
{if isset($item.condition)}
	{assign var='condition' value=$form->callElementMethod($item.condition)}
{else}
	{assign var='condition' value=true}
{/if}


{if $condition}
	<div class="form_items{if $formErrors.$fieldName} form_error{/if}{if !empty($item.trClass)} {$item.trClass}{/if}">
	<span class="form_label">
		{translations name="{$translationGroup}.{$fieldName}"}
	</span>
		<div class="form_field">
			<select class="{if !empty($item.class)}{$item.class} {/if}select_multiple" multiple="multiple" name="{$formNames.$fieldName}[]" autocomplete='off'>
				<option value=''></option>
				{if is_array($options)}
					{foreach $options as $option}
						<option value='{$option.id}'{if !empty($option.select)} selected="selected"{/if}>
							{if !empty($option.level)}{section name="level" start=0 loop=$option.level}&nbsp;&nbsp;{/section}{/if}
							{if !empty($translated)}
								{translations name="{$translated}.{$option.title}"}
							{else}
								{$option.title}
							{/if}
						</option>
					{/foreach}
				{/if}
			</select>
		</div>
		{include file=$theme->template('component.form_help.tpl')}
	</div>
{/if}