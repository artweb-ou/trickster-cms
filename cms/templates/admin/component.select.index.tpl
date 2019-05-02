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
{if isset($item.rightCell) && is_array($item.rightCell) && !empty($item.rightCell['tpl'])}
	{assign var='rightCellTpl' value=$item.rightCell['tpl']}
{/if}
<div class="form_items{if !empty($item.trClass)} {$item.trClass}{/if}">
	<div class="form_label">
		{translations name="{$structureType}.{$fieldName}"}
	</div>
	<div class="form_field">
		<select class="{if !empty($item.class)}{$item.class} {/if}dropdown_placeholder" name="{$formNames.$fieldName}" autocomplete='off'>
			{if !empty($item.defaultRequired)}
				<option value=""></option>
			{/if}
			{if is_array($options)}
				{foreach $options as $value=>$title}
					<option value="{$value}"{if $formData.$fieldName == $value} selected="selected"{/if}>
						{if is_numeric($title)}
							{$title}
						{else}
							{translations name="{$translationGroup}.{$title}"}
						{/if}
					</option>
				{/foreach}
			{/if}
		</select>
	</div>
	{if !empty($rightCellTpl)}
	<div class="form_field">
		{assign var='rightCellFieldName' value=''}
		{if !empty($item.rightCell['fieldName'])}
			{$rightCellFieldName=$item.rightCell['fieldName']}
		{/if}
		{include file=$theme->template($rightCellTpl) fieldName = $rightCellFieldName}
	</div>
	{/if}
	{include file=$theme->template('component.form_help.tpl')}
</div>