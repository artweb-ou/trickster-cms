{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

{capture assign="moduleContent"}
	{capture  assign="moduleTitle"}<div class='news_mailform_title'>{$element->title}</div>{/capture}
	{if $element->description}
		<div class="news_mailform_description html_content">
			{$element->description}
		</div>
	{/if}
	<form action="{$currentElement->URL}" class='news_mailform_form' method="post" enctype="multipart/form-data" role="form">
		<div class='news_mailform_form_contents'>
			<input type="hidden" value="{$element->id}" name="id" />
			<input type="hidden" value="subscribe" name="action" />
			<input type="text" class='input_component news_mailform_input' value="" name="{$formNames.email}" placeholder="{translations name='subscribe.enteryouremail'}"/>
			<div class="news_mailform_controls">
				<span class="button news_mailform_button">
					<span class='button_text'>
						{translations name='subscribe.subscribe'}
					</span>
				</span>
			</div>
		</div>
	</form>
{/capture}

{assign moduleClass "newsmailform_block"}
{assign moduleContentClass "news_mailform_block"}

{include file=$theme->template("component.columnmodule.tpl")}