{* INTRODUCTION *}
{if $element->introduction}
	<div class='product_details_intro toggleable_component'>
		<div class="product_details_parameter_group_header toggleable_component_trigger"><div class="toggleable_component_marker"></div>{translations name='product.introduction'}</div>
		<div class="toggleable_component_content">
			{$element->introduction}
		</div>
	</div>
{/if}