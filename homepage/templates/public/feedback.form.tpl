{capture assign="moduleContent"}
	{assign var='formData' value=$element->getFormData()}
	{assign var='formErrors' value=$element->getFormErrors()}
	{assign var='formNames' value=$element->getFormNames()}

	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	<form action="{$currentElement->URL}" class='feedback_form' method="post" enctype="multipart/form-data" role="form">
		<div class='feedback_form_container'>

			{*{if $element->title != ''}<h1 class="feedback_heading">{$element->title}</h1>{/if}*}
			{if $element->content != ''}
				{if !$element->resultMessage and !$element->errorMessage}
					<div class='feedback_content html_content ajax_form_hide_on_success'>
						{$element->content}
					</div>
				{/if}
			{/if}

			<div class='feedback_form_block'>

				<div class='form_result_message ajax_form_success_message'>
				</div>

				<div class='form_error_message ajax_form_error_message' role="alert">
				</div>

				{if !$element->resultMessage}
					<div class="ajax_form_hide_on_success">
						<div class='feedback_form_groups'>
							{foreach from=$element->getCustomFieldsGroups() item=groupElement name=groups}
								{include file=$theme->template("feedback.form.group.tpl") element=$groupElement}
							{/foreach}
						</div>
						<div class='form_controls'>

							<table class='form_table'>
								<tr class=''>
									<td class='form_label'></td>
									<td class='form_star'></td>
									<td class='form_field'>
										<span tabindex="0" class="button ajax_form_submit feedback_submit"><span class='button_text'>{if $element->buttonTitle}{$element->buttonTitle}{else}{translations name="feedback.send"}{/if}</span></span>
									</td>
									<td class='form_extra'></td>
								</tr>
							</table>
						</div>
					</div>
				{/if}
				<input type="hidden" value="{$element->id}" name="id" />
				<input type="hidden" value="send" name="action" />
			</div>
		</div>
	</form>
{/capture}

{assign moduleClass "feedback_block"}
{assign moduleTitleClass "feedback_heading"}
{assign moduleAttributes "id=\"feedback-form-{$element->id}\""}
{include file=$theme->template("component.contentmodule.tpl")}
{include file=$theme->template('javascript.hiddenFieldsData.tpl')}
