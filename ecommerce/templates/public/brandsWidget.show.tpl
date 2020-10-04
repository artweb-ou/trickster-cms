{if $data = $element->getJsonInfo('api')}
	<div class="brands_widget">
		<script>
			window.brandsWidget = {$data};
		</script>
		<div class="brands_widget_left"></div>
		<div class="brands_widget_right"></div>
		<div class="brands_widget_content"></div>
	</div>
{/if}
