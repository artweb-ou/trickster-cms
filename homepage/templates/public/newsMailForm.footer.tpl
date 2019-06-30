{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

<div class="newsmailform_block">
	<div class='news_mailform_title'>{$element->title}</div>
	{if $element->description}
		<div class="news_mailform_description">
			<div class="news_mailform_description_inner html_content">
				{$element->description}
			</div>
		</div>
	{/if}
	<form action="{$currentElement->URL}" class='news_mailform_form' method="post" enctype="multipart/form-data" role="form">
		<div class='news_mailform_form_contents'>
			<input type="hidden" value="{$element->id}" name="id" />
			<input type="hidden" value="subscribe" name="action" />
			<div class="news_mailform_input_wrap">
				<input type="text" class='input_component news_mailform_input' value="" name="{$formNames.email}" placeholder="{if $element->subscribed}{translations name='subscribe.thanksforsubscribing'}{else}{translations name='subscribe.enteryouremail'}{/if}"/>
				{if !empty($inputIcon)}
					{$inputIcon}
				{else}
					{if $icon = $theme->getImageUrl("icon_mail.svg", false, false)}
						<img class="news_mailform_input_icon hidden" src="{$icon}" />
					{elseif $icon = $theme->getImageUrl("icon_mail.png", false, false)}
						<img class="news_mailform_input_icon" src="{$icon}" />
					{/if}
				{/if}
			</div>
			<div class="news_mailform_controls">
				<a class="button news_mailform_button" href='{$element->URL}'>
					<span class='button_text'>
						{translations name='subscribe.subscribe'}
					</span>
				</a>
			</div>
		</div>
	</form>
</div>