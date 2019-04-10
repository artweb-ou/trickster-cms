<html>
	<head>
	</head>
	<body>
		<div style='display: none'>
			{$formData}
			<script type='text/javascript'>
			window.onload = function()
			{ldelim}
				document.forms[0].submit();
			{rdelim}
			</script>
		</div>
	</body>
</html>