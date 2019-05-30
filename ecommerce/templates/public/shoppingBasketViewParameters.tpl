{assign var="views" value=[]}
{assign var="viewsArr" value=''}

{$views['imageCell'] = "imageElement"}
{$views['infoCell'] = "tableTitleElement, titleElement, codeElement, descriptionElement"}
{$views['amountCell'] = ""}
{$views['priceCell'] = "fullPriceElement, salesPriceElement"}
{$views['removeCell'] = "deleteElementButton"}
{foreach $views as $viewKey=>$viewValue }
	{$viewsArr = $viewsArr|cat:' data-'|cat:$viewKey|cat:'="'|cat:$viewValue|cat:'"'}
{/foreach}
{assign var="viewParameters" value=$viewsArr scope=parent}