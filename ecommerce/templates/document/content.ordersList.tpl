<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style>
        {literal}
        body {
            font-size: 11px;
            font-family: Times, serif;
            margin: 50pt 50pt 50pt 50pt;
        }

        .orders_list_heading {
            margin-bottom: 10px;
        }

        .orders_list_heading h1 {
            margin-left: 0;
            margin-bottom: 5px;
        }

        .orders_list_heading_new {
            visibility: hidden;
        }

        .orders_list_heading_downloadpdf {
            display: inline-block;
            background-color: #d35050;
            padding: 5px 10px;
            border: 1px solid #800000;
            color: #fff;
            font-size: 11px;
            font-weight: normal;
            margin-left: 15px;
        }

        .orders_list_heading_displaypdf {
            display: inline-block;
            background-color: #d35050;
            padding: 5px 10px;
            border: 1px solid #800000;
            color: #fff;
            font-size: 11px;
            font-weight: normal;
            margin-left: 15px;
        }

        .orders_list_table {
            border-collapse: collapse;
            margin: 20px auto 0;
            width: 98%;
        }

        .orders_list_table .orders_list_table_head td {
            border-collapse: collapse;
            border: 1px solid #fff;
            padding: 3px 5px;
            background-color: #a0a0a0;
            color: #fff;
            text-align: center;
        }

        .orders_list_table_head select {
            font-size: 11px;
            display: block;
        }

        .orders_list_table .orders_list_table_rows td {
            border: 1px solid #d8d8d8;
            border-collapse: collapse;
            padding: 3px 5px;
        }

        .orders_list_row_new td {
            background-color: #f3f3ff;
        }

        .orders_list_row_payedprice {
            text-align: center;
            white-space: nowrap;
        }

        .orders_list_row_deliveryprice {
            text-align: center;
            white-space: nowrap;
        }

        .orders_list_row_date {
            white-space: nowrap;
        }

        .orders_list_row_remove {
            text-align: center;
        }

        .orders_list_table .orders_list_table_footer td {
            border-style: none;
            border-collapse: collapse;
            padding: 3px 5px;
            text-align: center;
        }

        .orders_list_table .orders_list_table_footer .orders_list_table_footer_name {
            border-collapse: collapse;
            border: 1px solid #fff;
            padding: 3px 5px;
            background-color: #a0a0a0;
            color: #fff;
        }

        .orders_list_table .orders_list_table_footer .orders_list_table_payedtotal,
        .orders_list_table .orders_list_table_footer .orders_list_table_deliverytotal {
            border: 1px solid #d8d8d8;
            border-collapse: collapse;
        }

        {/literal}
    </style>
</head>
<body>
<div class='orders_list_heading'>
    <h1>
        {translations name='orderslist.heading'} <span class="orders_list_heading_start">{$element->startDate}</span> -
        <span class="orders_list_heading_end">{$element->endDate}</span>
    </h1>
</div>
<table class='orders_list_table'>
    <thead class="orders_list_table_head">
    <tr>
        <td>
            Nr.
        </td>
        <td>
            {translations name='orderslist.ordernumber'}
        </td>
        <td>
            {translations name='orderslist.payedamount'}
        </td>
        <td>
            {translations name='orderslist.deliveryprice'}
        </td>
        <td>
            {translations name='orderslist.discount'}
        </td>
        <td>
            {translations name='orderslist.productsprice'}
        </td>
        <td>
            {translations name='orderslist.status'}
        </td>
        <td>
            {translations name='orderslist.date'}
        </td>
        <td>
            {translations name='orderslist.payer'}
        </td>
        <td>
            {translations name='orderslist.payeremail'}
        </td>
    </tr>
    </thead>

    <tbody class="orders_list_table_rows">
    {foreach from=$element->getContentList() item=order name=orders}
        <tr class="orders_list_row orders_list_row_{$order->getOrderStatus()}">
            <td class="orders_list_row_number">{$smarty.foreach.orders.iteration}</td>
            <td class="orders_list_row_ordernumber">{$order->invoiceNumber}</td>
            <td class="orders_list_row_payedprice">{$order->getPayedPrice()} {$order->currency}</td>
            <td class="orders_list_row_deliveryprice">{if $order->getDeliveryPrice() !== ""}{$order->getDeliveryPrice()} {$order->currency}{/if}</td>
            <td class="orders_list_row_discount">{$order->getDiscountAmount()} {$order->currency}</td>
            <td class="orders_list_row_productsprice">{$order->getProductsPrice()} {$order->currency}</td>
            <td class="orders_list_row_status">{$order->getOrderStatusText()}</td>
            <td class="orders_list_row_date">{$order->dateCreated}</td>
            <td class="orders_list_row_date">{$order->payerFirstName} {$order->payerLastName}</td>
            <td class="orders_list_row_date">{$order->payerEmail}</td>
        </tr>
    {/foreach}
    </tbody>
    <tfoot class="orders_list_table_footer">
    <tr>
        <td></td>
        <td></td>
        <td class="orders_list_table_footer_name">
            {translations name='orderslist.total'}:
        </td>
        <td class="orders_list_table_footer_name">
            {translations name='orderslist.total'}:
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td class="orders_list_table_payedtotal">{$element->payedAmount|round:"2"}</td>
        {if $element->deliveryPrice !== ''}
            <td class="orders_list_table_deliverytotal">{$element->deliveryPrice|round:"2"}</td>{/if}
        <td></td>
        <td></td>
    </tr>
    </tfoot>
</table>
</body>
</html>