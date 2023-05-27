<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
<table width="720">
    <tbody>
    <tr>
    <td rowspan="2" width="361">
    <h2>One Shop</h2>
    <p>Blvd 112 Russian Federation, <br/>Tuek Thla, Sen Sok, <br/> Phnom Penh</p>
    <p> Phone : +855 0962797338 <br/> Fax :  +855 01456</p>
    <p > <span > Order Id # : {{ $responseData['order_id'] }} </span> </p>

    </td>
    <td width="359">
    <h1>INVOICE</h1>
    </td>
    </tr>
    <tr>
    <td width="359">
    <p>Invoice No # {{ $responseData['invoice_no']}}</p>
    <p>Date # : {{ $responseData['invoice_date']}} </p>

    </td>
    </tr>
    </tbody>
    </table>
    <p>&nbsp;</p>
    <table width="360">
    <tbody>

    <tr>
    <td width="100">
    <h4>To (Customer) :</h4>
    </td>

    </tr>
        <tr>
            <td width="360">
           Name   : {{ $responseData['customer_name']}}
            </td>
        </tr>
    <tr>
        <td width="360">
           Phone # : {{ $responseData['customer_phone']}}
        </td>
    </tr>
    <tr>
        <td width="360">
           Email   : {{ $responseData['customer_email']}}
        </td>
    </tr>

    </tbody>
    </table>
    <p>&nbsp;</p>

    <table width="550" border="1">
    <tbody>

    <tr>
    <td colspan="2" width="403">
    <p>DESCRIPTION</p>
    </td>
    <td width="126">
    <p align="center">SLOTS</p>
    </td>
    <td width="98">
    <p>SLOT PRICE ($)</p>
    </td>
    <td width="94">
    <p align="center">TOTAL</p>
    </td>
    </tr>
        {{$total = $Subtotal = 0 ;}}
        @foreach($responseData['orderProducts'] as $orderProduct)
            <tr>
            <td colspan="2" width="403">
            <p align="center">{{$orderProduct['product']['slug']}}</p>

            </td>
            <td width="126">
            <p align="center">{{$orderProduct->slots}}</p>
            </td>
            <td width="98">
            <p align="center">{{$orderProduct->amount}} </p>
            </td>
            <td width="94">
            <p align="center">${{ $total = $orderProduct->slots * $orderProduct->amount}} </p>
            </td>
            </tr>
            {{ $Subtotal += $orderProduct->slots * $orderProduct->amount }}
        @endforeach

    <tr>
    <td colspan="2" width="403">
    <p>&nbsp;</p>
    </td>
    <td width="126">
    <p>&nbsp;</p>
    </td>
    <td width="98">
    <p>&nbsp;</p>
    </td>
    <td width="94">
    <p>&nbsp;</p>
    </td>
    </tr>

    <tr>
    <td colspan="2" rowspan="4" width="430">
    <p>&nbsp;</p>
    </td>
    <td colspan="2" width="197">
    <p>SUBTOTAL ($) </p>
    </td>
    <td width="94">
    <p align="center">{{$Subtotal}}</p>
    </td>
    </tr>

    <tr>
    <td colspan="2" width="197">
    <p>SHIPPING &amp; HANDLING</p>
    </td>
    <td width="94">
    <p align="center">{{ $shipping = 0 ;}}</p>
    </td>
    </tr>
    <tr>
    <td colspan="2" width="197">
    <p >TOTAL ($)</p>
    </td>
    <td width="94">
    <p>=   &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span align="center"> &nbsp;${{ $full_total = $Subtotal + $shipping }}  </span>  </p>
    </td>
    </tr>
    </tbody>
    </table>

    <table>
<tbody>
<tr>
<td width="734">
  <p>Thank you for your business!</p>
<p>&nbsp;</p>
</td>
</tr>
</tbody>
</table>

    </body>
</html>
