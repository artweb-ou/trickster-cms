<textarea class="{if isset($className)}{$className}{/if}" name="{$name}">{$data}</textarea>
<script>
	CKEDITOR.on('instanceReady', function(ev) {
		ev.editor.dataProcessor.writer.indentationChars = '';
	});

	var editor = CKEDITOR.replace('{$name}',
	{
		customConfig: '{$controller->baseURL}/trickster/cms/js/ckeditor/config.js'
	});
	CKFinder.setupCKEditor(editor, '{$controller->baseURL}libs/ckfinder/');
</script>