{include file=$theme->template("component.ajaxsearch.tpl")}

<div class="form_items">
	<span class="form_label"></span>
	<div class="form_field">
		<div class="{$item.import_class}_import_info">
			<input class="replacementImage" type='hidden' name={$formNames.replacementImage}>
			<input class='{$item.import_class}_import button primary_button' type="button" value="{translations name='newsmailinfo.import'}" />
			<span class="{$item.import_class}_import_title"></span>
		</div>
	</div>
</div>