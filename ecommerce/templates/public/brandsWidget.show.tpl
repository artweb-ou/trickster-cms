{if $data = $element->getElementData()}
	<div class="brands_widget">
		<script>
			/*<![CDATA[*/
			window.brandsList = {$data|json_encode};
			/*]]>*/
		</script>
		<div class="brands_widget_left"></div>
		<div class="brands_widget_right"></div>
		<div class="brands_widget_content"></div>
	</div>
{/if}
