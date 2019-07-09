{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component product_form" method="post" enctype="multipart/form-data" class="product_form">
	<div class="form_fields">
        {foreach $formData.formParameters as $parametersGroup}
			<div class="form_items">
				<div class="form_label"></div>
				<div class="form_label heading">
					<h2 class="content_list_title">
                        {$groupsIndex.{$parametersGroup@key}->title|default:$groupsIndex.{$parametersGroup@key}->structureName}:
					</h2>
				</div>
			</div>
            {foreach $parametersGroup as $parameter}
                {if $parameter.type == 'productParameter'}
                    {foreach from=$parameter.values key=languageId item=value}
						<div class="form_items">
							<div class="form_label">
                                {$parameter.title}{if $languageId!='0'} ({$languageNames.$languageId}){/if}:
							</div>
							<div class="form_field">
								<input class="input_component" type="text" value="{$value}" name="{$formNames.formParameters}[{$parameter.id}][{$languageId}]" />
                                {include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="formParameters"}
							</div>
						</div>
                    {/foreach}
                {elseif $parameter.type == 'productSelection'}
					<div class="form_items">
						<div class="form_label">
                            {$parameter.title}:
						</div>
						<div class="form_field">
							<select class="select_multiple" multiple='multiple' name="{$formNames.formParameters}[{$parameter.id}][]">
								<option value=''>{translations name='label.notselected'}</option>
                                {foreach from=$parameter.options item=option}
									<option value='{$option.id}'{if $option.selected} selected='selected'{/if} {if $parameter.parameterType=="color"}style="background-color: #{$option.value}"{/if}>{$option.title}</option>
                                {/foreach}
							</select>
                            {include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="formParameters"}
						</div>
					</div>
                {/if}
            {/foreach}
        {/foreach}
	</div>
    {include file=$theme->template('component.controls.tpl') action="receiveParameters"}
</form>