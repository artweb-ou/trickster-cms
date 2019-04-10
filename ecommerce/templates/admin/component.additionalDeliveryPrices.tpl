{foreach from=$formData.formDeliveries item=deliveryType}
	<div class="form_items form_header">
		<div class="form_label">
			{translations name='delivery_prices.delivery_type'}
		</div>
		<div class="form_label">
			{translations name='delivery_prices.delivery_surcharge'}
		</div>
	</div>
	<div class="form_items">
		<div class="form_label">
			<div class="heading">
				<h3>{$deliveryType->title}</h3>
			</div>
		</div>
		<div class="form_label">

		</div>
	</div>
	<div class="form_items">
		<div class="form_label">
			{translations name='delivery_prices.all_regions'}
		</div>
		<div class="form_field">
			<input class="input_component narrow_input" type="text" value="{$element->getDeliveryPriceExtra($deliveryType->id, 0)}" name="{$formNames.formDeliveries}[{$deliveryType->id}][0][priceExtra]" />
			{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="formDeliveries"}
		</div>
	</div>
	{foreach $deliveryType->getCountriesList() as $country}
		{if $country->selected}
			<div class="form_items">
				<div class="form_label">
					{$country->title}
				</div>
				<div class="form_field">
					<input class="input_component narrow_input" type="text" value="{$element->getDeliveryPriceExtra($deliveryType->id, $country->id)}" name="{$formNames.formDeliveries}[{$deliveryType->id}][{$country->id}][priceExtra]" />
				</div>
			</div>
		{/if}
		{foreach from=$country->citiesList item=city}
			{if $city->selected}
				<div class="form_items">
					<div class="form_label">
						{$country->title} / {$city->title}
					</div>
					<div class="form_field">
						<input class="input_component narrow_input" type="text" value="{$element->getDeliveryPriceExtra($deliveryType->id, $city->id)}" name="{$formNames.formDeliveries}[{$deliveryType->id}][{$city->id}][priceExtra]" />
					</div>
				</div>
			{/if}
		{/foreach}
	{/foreach}
	<div class="form_items">
		<div class="form_label">
			{translations name='delivery_prices.delivery_active'}
		</div>
		<div class="form_field checkbox_cell">
			<input class='singlebox checkbox_placeholder' type="checkbox" name="{$formNames.formDeliveries}[{$deliveryType->id}][0][active]" value="1"{if $deliveryType->active} checked="checked"{/if}/>
		</div>
	</div>
{/foreach}