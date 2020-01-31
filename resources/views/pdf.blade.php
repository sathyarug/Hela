<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<style>
#tbl1 {
  border-collapse: collapse;
}

#tbl1,#tbl1 td {
  border: 1px solid black;
  font-size:12;
}

#tbl1,#tbl1 td {
  border: 1px solid black;
}

#tbl2,#tbl2 td {
  font-size:12;
}

#tbl3,#tbl3 td {
  font-size:12;
}

#tbl4,#tbl4 td {
  border: 0px solid black;
  font-size:9;
}

#background{
    position:absolute;
    z-index:0;
    #background:white;
    display:block;
    min-height:50%;
    min-width:50%;
    color:yellow;
    top: 270px;
    left: 200px;
    opacity: 0.5;

}

#content{
    position:absolute;
    z-index:1;
}

#bg-text
{
    color:#E0282A;
    font-size:50px;
    transform:rotate(300deg);
    -webkit-transform:rotate(300deg);
}


</style>
</head>

<body>

  <!--<div id="background">
  <p id="bg-text">NOT APPROVED</p>
	</div>-->


<div class="container" >
<div style="width:100%;">
<div style="float:left; width:20%;"><img src="http://test-surface.helaclothing.com/test/surfacedev/resources/images/hela.jpg"/></div>
<div style="float:left;width:80%;">
<div id="example2">
<div style="font-weight:bold;font-size:16px;">Foundation Garments (Pvt) Ltd</div>
<div>VAT NO: 114042005-7000 (Company Reg No:PV2646)</div>
<div>No.35Balapokuna Road Colombo 05,SRI LANKA</div>
<div>Tel:+94 11 4385400  Fax:+94 11 2769088</div>
<div>E-Mail:       Web: www.fdnsl.com</div></div>
  </div>
</div>

<h3 style="font-weight:bold; text-align:center;">Purchase Order</h3>
<table id="tbl3" width="100%" border="0" align="center">
  <tr>
    <td rowspan="2" valign="top"><strong>PO NO - {{$po}}</strong></td>
    <td width="15%"><strong>PO Date</strong></td>
    <td width="3%"><strong>:</strong></td>
    <td width="21%">{{$po_date}}</td>
  </tr>
  <tr>
    <td width="15%"><strong>PO Status</strong></td>
    <td><strong>:</strong></td>
    <td>{{$po_status}}</td>
  </tr>
</table>

<table id="tbl2" width="100%">
    <tr>
      <td width="47%" height="126">
      <table width="100%" height="121" border="0" align="left">
        <tr>
          <td width="30%"><strong>Supplier Name</strong></td>
          <td width="5%"><strong>:</strong></td>
          <td width="65%">{{$supplier_name}}</td>
        </tr>
        <tr>
          <td valign="top"><strong>Address</strong></td>
          <td valign="top"><strong>:</strong></td>
          <td valign="top" width="140">
          <p>{{$supplier_address1}},</p>
          <p>{{$supplier_address2}},</p>
          <p>{{$supplier_city}} - {{$supplier_country}}</p></td>
        </tr>
        <tr>
          <td><strong>Pay.Method</strong></td>
          <td><strong>:</strong></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="37">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      </td>
      <td width="6%">&nbsp;</td>
      <td width="47%" valign="top"><table width="97%" align="left">
          <tr>
            <td width="97%" height="17"><p><strong>Deliver To: </strong></p></td>
          </tr>
          <tr>
            <td><p>{{$loc_name}},</p><p>{{$loc_address_1}},</p><p>{{$loc_address_2}}</p></td>
          </tr>
          <tr>
            <td height="22" valign="top"><p><strong>Invoice To:</strong></p></td>
          </tr>
          <tr>
            <td height="37" valign="top">&nbsp;</td>
          </tr>
      </table></td>
    </tr>
  </table>


  <table id="tbl1" width="100%" align="center">
    <tr>
      <td width="6%" align="center"><strong>Line No</strong></td>
      <td width="13%" align="center"><strong>Style</strong></td>
      <td width="13%" align="center"><strong>Description</strong></td>
      <td width="7%" align="center"><strong>Del Date</strong></td>
      <td width="7%" align="center"><strong>Size</strong></td>
      <td width="7%" align="center"><strong>Color</strong></td>
      <td width="7%" align="center"><strong>Unit</strong></td>
      <td width="12%" align="center"><p><strong>Unit Price USD</strong></p></td>
      <td width="5%" align="center"><strong>Qty</strong></td>
      <td width="9%" align="center"><strong>Value</strong></td>
    </tr>
    {{  $tot =0 }}
    {{  $req =0 }}
    @foreach ($data as $d)
    <tr>

      <td>{{ $d->line_no }}</td>
      <td>{{ $d->style_no }}</td>
      <td>{{ $d->master_description }}</td>
      <td>{{ $d->deli_date}}</td>
      <td>{{ $d->size_name }}</td>
      <td>{{ $d->color_name }}</td>
      <td>{{ $d->uom_code }}</td>
      <td align="right">{{ $d->unit_price }}</td>
      <td align="right">{{ $d->req_qty }}</td>
      <td  align="right">{{ $d->tot_qty }}</td>
    </tr>
    {{ $req += $d->req_qty }}
    {{ $tot += $d->tot_qty }}
    @endforeach
    <tr>
      <td colspan="8" align="center"><strong>Total</strong></td>
      <td  align="right"><strong>{{ $req }}</strong></td>
      <td  align="right"><strong>{{ $tot }}</strong></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <table id="tbl4" width="100%">
    <tr>
      <td colspan="2">
      <ol><li>This Agreement is the entire agreement between the Parties with respect to the supply of goods and supersedes all prior agreements and understanding with respect to this subject. This
agreement may be amended only by written agreement executed by both Parties.
</li><li> Upon Delivery of the order, the Purchaser shall have the right to reject the Order in relation to the quantity delivered at any time. The Purchaser shall inform the Supplier at the time of the rejection
only. The purchaser shall neither be liable to return any rejected quantities to the Supplier nor required to pay for same
</li><li>The Supplier hereby agrees to supply not in excess of + / - 3% per each line which shall not be greater than the total quantity shown in the purchase order. The Purchaser shall not be liable for
any products shipped in excess of this limit. The Purchaser is not be liable to return to the Suppliers the excess products supplied by the Supplier over the agreed limit or will not be liable to pay for
such quantities above the agreed excess limit .
</li><li> This purchase order is based on the technical specifications of the product as per the Supplier's Performa invoice, and shall not be deviated and/or amended in any manner what so ever, without
the prior written approval of the Purchaser.
</li><li> The Supplier shall adhere strictly to the terms and conditions relating to price, delivery and packaging stipulated in the purchase order.
</li><li> Unless otherwise agreed in writing by the Purchaser and the Supplier, failure on the part of the Supplier to meet deadlines shall result in airing of the raw material entirely on the Supplier's
account. The Supplier here by agrees to indemnify the Purchaser and hold the Purchaser saved and harmless against any delays and/or the Supplier's inability to meet deadlines.
</li><li>The Supplier shall submit originals of all mandatory laboratory test reports and shipping samples as required by the Purchaser, before any bulk shipment is affected.
</li><li> The Supplier shall not use any metal nails, pins, clips, staples or the likewise for the purpose of packing. The Supplier hereby ensures sand guarantees to the Purchaser that all cartons and
packing materials shall be anti-metal. 9. Payment will be made on the agreed payment terms and in the currency stipulated in the purchase order.
</li><li> All suppliers from the SAARC region should send the GSP Certificate along with each shipment.
</li><li>All Suppliers from EU region should send the EUR1/ INF2 Certificates along with each shipment.
</li><li> Shipping Documents: Manually signed Commercial Invoice/Packing List & Original Bill of Lading should be couriered, If the BL is surrendered or if through Bank, Copy would be sufficient.Courier
charges of the shipping documents should be on Supplier's account.
</li><li> Hard copies must reach us via courier (Preferred through DHL) within 7 days from the ETD. If the Sea transit is less than 7 days, please courier the Commercial Invoice/Packing List prior to the
Sailing & surrender the Bill of Lading. Scanned copies of the same documents should reach us via e-mail within two days. If the above is not fulfilled there will be a charge back of US$50 per
shipment & demurrages if any.
</li><li> Actual carton dimensions/Gross weight/ Net weight/ Volume weight should be clear marked on the Invoice & P/L. Shipping documents should be as per the correct INCO terms & Payment
terms.
</li><li> Relevant HS Code per item should be marked in the Description column on the Commercial Invoice. Item Description should be clearly indicated (E.g. Knit Fabric , Elastics , Lace, etc ) on the
Invoice. Ship Mode should be indicated on the Invoice.
</li><li> If the above (point 10 to 15) is not fulfilled there will be a charge back of US$ 50 per shipment & demurrages if any.
</ol></td>
    </tr>
    <tr>
      <td width="84%" scope="col"><p>&nbsp;</p><b>THIS IS A SYSTEM GENERATED DOCUMENT, SIGNATURES NOT REQUIRED<b></td>
      <td width="16%" scope="col"><p>&nbsp;</p>A Division Of</td>
    </tr>
  </table>
</div>

</body>

<style>
#example1 {
  margin: auto;
  width: 50%;
  border: 1px solid #73AD21;
  padding: 10px;
}

#example2 {
  margin: auto;
  width: 70%;
  padding: 10px;
  font-size:12px;
}

</style>
</body>
</html>
