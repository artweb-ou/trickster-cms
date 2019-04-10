{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component comment_form" method="post" enctype="multipart/form-data">
	<div class="tabs_content_item">
		<table class="form_table">
			<tr{if $formErrors.userId} class="form_error"{/if}>
				<td class="form_label">
					{translations name='comment.user'}:
				</td>
				<td colspan="2">
					<select class="comment_form_user_select" name="{$formNames.userId}" autocomplete='off'>
						{assign var="userElement" value=$element->getUserElement()}
						{if $userElement}
							<option value='{$userElement->id}' selected="selected">
								{$userElement->getTitle()}
							</option>
						{/if}
					</select>
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.author'}:
				</td>
				<td>
					<input class='input_component' type="text" value="{$formData.author}" name="{$formNames.author}" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="author"}
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.email'}:
				</td>
				<td>
					<input class='input_component' type="text" value="{$formData.email}" name="{$formNames.email}" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="email"}
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.content'}:
				</td>
				<td>
					<textarea class='textarea_component' name="{$formNames.content}">{$formData.content}</textarea>
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="content"}
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.ip'}:
				</td>
				<td>
					<input class='input_component' type="text" value="{$formData.ipAddress}" name="{$formNames.ipAddress}" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="ipAddress"}
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.comment_approved'}:
				</td>
				<td>
					<input class="checkbox_placeholder" type="checkbox" value="1" name="{$formNames.approved}"{if $formData.approved} checked="checked"{/if} />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="approved"}
				</td>
			</tr>
			<tr>
				<td class="form_label">
					{translations name='comment.replies'}:
				</td>
				<td>
					{foreach $element->getReplies() as $reply}
						<div class="reply">
							<span class="reply_author">{$reply->author}</span>
							<a class="icon icon_edit" href="{$reply->URL}"></a>
							<a href="{$reply->URL}id:{$reply->id}/action:delete" class='icon icon_delete'></a>
							<br />
							<div class="reply_datetime">{$reply->dateTime}</div>
							<div class="html_content reply_content">{$reply->content|html_entity_decode}</div>
						</div>
					{/foreach}
				</td>
			</tr>
		</table>

	</div>
	{include file=$theme->template('component.controls.tpl')}
</form>