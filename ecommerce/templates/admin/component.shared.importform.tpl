<script>

	window.importFormRecords = {json_encode($importFormRecords)};

</script>
<div class="form_items">
	<div class="form_label">
		{translations name='importform.origin'}
	</div>
	<div class="form_label">
		{translations name='importform.import_id'}
	</div>
	<div class="form_field"></div>
</div>
<div class="form_items importform_form_adder">
	<div class="form_field">
		<select class="importform_form_adder_origin dropdown_placeholder">
			{foreach $importFormOrigins as $origin}
				<option value="{$origin}">
					{$origin}
				</option>
			{/foreach}
		</select>
	</div>
	<div class="form_field">
		<input class='importform_form_adder_id input_component' type="text" />
	</div>
	<div class="form_field">
		<a class="importform_form_adder_add button primary_button" href="#">
			{translations name='importform.add_record'}
		</a>
	</div>
</div>