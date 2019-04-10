<div class="deliverytype_form_prices_component">
	<div class="form_fields deliverytype_form_prices_table">
		<div class="deliverytype_form_prices_rows">
			<div class="form_items">
				<span class="form_label">
					{translations name='deliverytype.allowed_region'}
				</span>
				<span class="form_label">
					{translations name='deliverytype.price'}
				</span>
			</div>
			{foreach from=$element->getCountriesList() item=country}
				{if $country->selected}
					<div class="prices_row prices_row_id_{$country->id} form_items">
						<span class="form_label">
							{$country->title}
						</span>
						<div class="form_field">
							<input class='input_component' type="text" value="{$country->deliveryPrice}" name="{$formNames.prices}[{$country->id}]" />
							{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
						</div>
						<div class="form_field">
							<a class="prices_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
						</div>
					</div>
				{/if}
				{foreach from=$country->citiesList item=city}
					{if $city->selected}
						<div class="prices_row prices_row_id_{$city->id} form_items">
							<span class="form_label">
								{$country->title} / {$city->title}
							</span>
							<div class="form_field">
								<input class='input_component' type="text" value="{$city->deliveryPrice}" name="{$formNames.prices}[{$city->id}]" />
								{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
							</div>
							<div class="form_field">
								<a class="prices_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
							</div>
						</div>
					{/if}
				{/foreach}
			{/foreach}
			<div class="form_items deliverytype_form_separator">
				<div class="form_field seperator"></div>
				<div class="form_field seperator"></div>
			</div>
			<div class="prices_new form_items">
				<div class="form_field">
					<select class="dropdown_placeholder prices_new_selector">
						{foreach $element->getCountriesList() as $country}
							{foreach from=$country->citiesList item=city}
								<option value="{$city->id}" {if $city->selected}selected="selected"{/if}>{$country->title} / {$city->title}</option>
								{foreachelse}
								<option value="{$country->id}" {if $country->selected}selected="selected"{/if}>{$country->title}</option>
							{/foreach}
						{/foreach}
					</select>
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="input"}
				</div>
				<div class="form_field">
					<input class='prices_new_price input_component' type="text" value="0" name="{$formNames.prices}" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
				</div>
				<div class="form_field">
					<a class="prices_new_add button primary_button" href="">{translations name="deliverytype.add"}</a>
				</div>
			</div>
		</div>
	</div>
</div>