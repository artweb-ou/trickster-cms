<div class="deliverytype_form_fields_component">
	<div class="form_fields deliverytype_form_fields_table">
		<div class="deliverytype_form_fields_rows">
			<div class="form_items">
				<span class="form_label">
					{translations name="deliverytype.field_name"}
				</span>
				<span class="form_label">
					{translations name="deliverytype.active"}
				</span>
			</div>
			{foreach from=$element->fieldsList item=field}
				{if $field->selected}
					<div class="fields_row fields_row_id_{$field->id} form_items">
						<span class="form_label">
							{$field->title}
						</span>
						<div class="form_field checkbox_container">
							<input class='fields_required checkbox_placeholder' type="checkbox" value="1" {if $field->required}checked="checked"{/if}/>
							<input class='fields_hidden' type="hidden" name="{$formNames.fields}[{$field->id}]" value="{$field->required}" />
						</div>
						<div class="form_field">
							<a class="fields_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
						</div>
					</div>
				{/if}
			{/foreach}
			<div class="form_items deliverytype_form_separator">
				<div class="form_field seperator"></div>
				<div class="form_field seperator"></div>
			</div>
			<div class="fields_new form_items">
				<div class="form_field">
					<select class="dropdown_placeholder fields_new_selector">
						{foreach from=$element->fieldsList item=field}
							<option value="{$field->id}" {if $field->selected}selected="selected"{/if}>{$field->title}</option>
						{/foreach}
					</select>
				</div>
				<div class="form_field">
					<input class='fields_new_field checkbox_placeholder' name="{$formNames.fields}" type="checkbox" value="0" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="fields"}
				</div>
				<div class="form_field">
					<a class="fields_new_add button primary_button" href="">{translations name="deliverytype.add"}</a>
				</div>
			</div>
		</div>
	</div>
</div>