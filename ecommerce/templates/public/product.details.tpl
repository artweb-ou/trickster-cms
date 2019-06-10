{capture assign="moduleTitle"}
	{include file=$theme->template("product.details.title.tpl")}
{/capture}
{capture assign="moduleContent"}
	{include file=$theme->template("product.details.top.tpl")}
	{stripdomspaces}
		<div class="product_details_main">
			{include file=$theme->template("product.details.left.tpl")}
			{include file=$theme->template("product.details.right.tpl")}
		</div>
		{include file=$theme->template("product.details.center.tpl")}
		{include file=$theme->template("product.details.connected_products.tpl")}
		{include file=$theme->template("product.details.connected_categories.tpl")}
	{/stripdomspaces}
	<script>
		window.productDetailsData = {json_encode($element->getElementData(true))};
	</script>
{/capture}
{assign moduleClass "product_details_block"}
{assign moduleTitleClass "product_details_title"}
{assign moduleContentClass "product_details productid_"|cat:$element->id}
{include file=$theme->template("component.contentmodule.tpl")}