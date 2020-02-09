<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Trickster Installer</title>
		<meta name="robots" content="noindex, nofollow"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" type="text/css" href="{$controller->baseURL}css/set:{$theme->getCode()}/file:index.css"/>
	</head>
	<body>
		<div class="wrap">
			<div class="logo_wrap">
				<img class="logo" src="/trickster/cms/images/admin/trickster_logo.png" />
			</div>
			<div class="content">
				{if !empty($error)}
					<div class="error">
						{$error}
					</div>
				{/if}
				{if $installProgress == 0}
				<form class="form_component" method="post" enctype="multipart/form-data" action="{$controller->baseURL}installer/install/">
					<fieldset class="form_fieldset">
						<legend>Database credentials</legend>
						{foreach $dbFields as $field}
							<div class="form_field form_field_text">
								<label for="form_control_{$field}">
									{ucfirst(str_replace('db_', '', $field))}:
								</label>
								<input id="form_control_{$field}" type="text" name="{$field}" value="{$input.$field}" />
							</div>
						{/foreach}
					</fieldset>
					<fieldset class="form_fieldset">
						<legend>Plugins</legend>
						{foreach $plugins as $plugin}
							<div class="form_field form_field_checkbox">
								<input id="form_control_db_plugin_{$plugin}" type="checkbox" name="plugin[{$plugin}]" value="1"{if is_array($input) && isset($input['plugin'][$plugin])} checked="checked"{/if} />
								<label for="form_control_db_plugin_{$plugin}">
									{$plugin}
								</label>
							</div>
						{/foreach}
					</fieldset>
					<fieldset class="form_fieldset">
						<legend>API key</legend>
						{foreach $licenceFields as $field}
							<div class="form_field form_field_text">
								<label for="form_control_{$field}">
									{ucfirst(str_replace('licence_', '', $field))}:
								</label>
								<input id="form_control_{$field}" type="text" name="{$field}" value="{$input.$field}" />
							</div>
						{/foreach}
					</fieldset>
					<input class="button" type="submit" value="Install"/>
				</form>
				{elseif $installProgress == 2}
					CMS has been installed! <a href="/">Proceed to my site</a>.
				{/if}
			</div>
		</div>
	</body>
</html>
