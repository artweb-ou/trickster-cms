{*{if isset($item.method)}*}
{*	{assign var='options' value=$form->callElementMethod($item.method)}*}
{*{elseif isset($item.property)}*}
{*	{assign var='options' value=$form->getElementProperty($item.property)}*}
{*{elseif isset($item.select_options)}*}
{*	{assign var='options' value=$form->getElementOptions($item.select_options)}*}
{*{else}*}
{*	{assign var='options' value=""}*}
{*{/if}*}
{*{if !empty($item.translationGroup)}*}
{*	{assign var='translated' value = $item.translationGroup}*}
{*{/if}*}



{if isset($item.method)}
	{assign var='options' value=$form->callElementMethod($item.method)}
{elseif isset($item.property)}
	{assign var='options' value=$form->getElementProperty($item.property)}
{elseif isset($item.options)}
	{assign var='options' value=$item.options}
{/if}
{if !empty($item.translationGroup)}
	{$translationGroup = $item.translationGroup}
{else}
	{$translationGroup = $structureType}
{/if}


{if isset($item.condition)}
	{assign var='condition' value=$form->callElementMethod($item.condition)}
{else}
	{assign var='condition' value=true}
{/if}
{assign var='currentSelected' value=''}


{if $condition}
	<div class="form_items{if $formErrors.$fieldName} form_error{/if}{if !empty($item.trClass)} {$item.trClass}{/if}">
	<span class="form_label">
		{translations name="{$translationGroup}.{$fieldName}"}
	</span>
		<div class="form_field">
			<select class="{if !empty($item.class)}{$item.class} {/if}select_multiple" multiple="multiple" name="{$formNames.$fieldName}[]" autocomplete='off'>
				<option value=''></option>
				{if is_array($options)}
					{foreach $options as $value=>$title}
						{if $formData.$fieldName == $value}
							{$currentSelected = ' selected="selected"'}
						{/if}
						<option value="{$value}"{$currentSelected}>
{*						<option value='{$value}'{if !empty($option.select)} selected="selected"{/if}>*}
							{if is_numeric($title)}
								{$title}
							{else}
								{translations name="{$translationGroup}.{$title}"}
							{/if}
						</option>
						{$currentSelected = ''}
					{/foreach}
				{/if}
			</select>
		</div>
		{include file=$theme->template('component.form_help.tpl')}
	</div>
{/if}