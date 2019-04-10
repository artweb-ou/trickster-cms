{* CONNECTED PRODUCTS *}
{$connectedTemplate = $theme->template('product.details.connected.tpl')}
{include file=$connectedTemplate products=$element->getConnectedProducts() title="{translations name='product.connectedproducts'}"}
{include file=$connectedTemplate products=$element->getConnectedProducts2() title="{translations name='product.connectedproducts2'}"}
{include file=$connectedTemplate products=$element->getConnectedProducts3() title="{translations name='product.connectedproducts3'}"}