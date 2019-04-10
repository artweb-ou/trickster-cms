{include file=$theme->template('block.newelement.tpl') allowedTypes=$currentElement->getAllowedChildStructureTypes()}
<div>
	<a href='{$currentElement->URL}'>{translations name='label.back'}</a>
</div>
<p>
	{$currentElement->exportText}
</p>