<?php
/**
 * Created by "Unleashed Studios".
 * User: phi314
 * Date: 14/03/17
 * Time: 10:59
 */

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>

    <style>
        .invoice-box {
            max-width:800px;
            margin:auto;
            padding:30px;
            border:1px solid #eee;
            box-shadow:0 0 10px rgba(0, 0, 0, .15);
            font-size:16px;
            line-height:24px;
            font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color:#555;
        }

        .invoice-box table{
            width:100%;
            /*line-height: 12px;*/
            text-align:left;
        }

        .invoice-box table td{
            padding:5px;
            vertical-align:top;
        }

        .invoice-box table tr td:nth-child(2){
            text-align:right;
        }

        .invoice-box table tr.item td:nth-child(3), .invoice-box table tr.heading td:nth-child(3){
            text-align:right;
        }

        .invoice-box table tr.top table td{
            padding-bottom:20px;
        }

        .invoice-box table tr.top table td.title{
            font-size:45px;
            line-height:45px;
            color:#333;
        }

        .invoice-box table tr.information table td{
            padding-bottom:40px;
        }

        .invoice-box table tr.heading td{
            background:#eee;
            border-bottom:1px solid #ddd;
            font-weight:bold;
        }

        .invoice-box table tr.details td{
            padding-bottom:20px;
        }

        .invoice-box table tr.item td{
            border-bottom:1px solid #eee;
        }

        .invoice-box table tr.item.last td{
            border-bottom:none;
        }

        .invoice-box table tr.total td:nth-child(3){
            border-top:2px solid #eee;
            font-weight:bold;
            text-align: right;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td{
                width:100%;
                display:block;
                text-align:center;
            }

            .invoice-box table tr.information table td{
                width:100%;
                display:block;
                text-align:center;
            }
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="3">
                <table>
                    <tr>
                        <td class="title">
                            <h5>Dinesta Laundry</h5>
                        </td>

                        <td>
                            {{ __('Invoice') }} #: {{ $invoice->wash->code }}<br>
                            {{ __('Date') }}: {{ $created_at }}<br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="3">
                <table>
                    <tr>
                        <td>
                            Dinesta Laundry<br>
                            Jalan Cikutra No. 24<br>
                            Kota Bandung, 40333
                        </td>

                        <td>
                            {{ $invoice->user->name }}<br>
                            {{ $invoice->user->phone }}<br>
                            {{ $invoice->user->email }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>
                {{ __('Item') }}
            </td>

            <td>
                {{ __('Qty') }}
            </td>

            <td>
                {{ __('Price') }}
            </td>
        </tr>

        @foreach($wash_details as $wash_detail)
        <tr class="item">
            <td>
                {{ $wash_detail->item->name }}
            </td>

            <td>
                {{ $wash_detail->qty }}
            </td>

            <td>
                {{ money_format('%n', $wash_detail->price) }}
            </td>
        </tr>
        @endforeach

        <tr class="total">
            <td></td>
            <td></td>

            <td>
                Total: {{ money_format('%n', $invoice->total_price) }}
            </td>
        </tr>

        <tr class="heading">
            <td>
                {{ __('Item') }}
            </td>

            <td>
                {{ __('Qty') }}
            </td>

            <td>
                {{ __('Price') }}
            </td>
        </tr>

        @foreach($wash_details as $wash_detail)
            <tr class="item">
                <td>
                    {{ $wash_detail->item->name }}
                </td>

                <td>
                    {{ $wash_detail->qty }}
                </td>

                <td>
                    {{ money_format('%n', $wash_detail->price) }}
                </td>
            </tr>
        @endforeach

        <tr class="total">
            <td></td>
            <td></td>

            <td>
                Total: {{ money_format('%n', $invoice->total_price) }}
            </td>
        </tr>
    </table>
</div>
</body>
</html>


