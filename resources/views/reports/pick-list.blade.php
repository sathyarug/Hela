@extends('reports.common.template')

@section('title')
    Costing Details
@stop

@section('content')   

<div class="container">
	<div class="row">
		<div class="col-md-8 col-sm-offset-2 form-holder">
			
			<div class="col-md-12">
				<div class="logo">
					<img src="{{ URL::asset('assets\images\logo.jpg') }}"/>
				</div>
				<div class="address"> 
					@foreach ($company as $row)
					<div class="company-name">{{ $row->company_name }}</div>
					<div class="company-address">{{ $row->loc_name }}</div>
					<div class="company-address">{{ $row->loc_address_1 }},{{ $row->loc_address_2 }},{{ $row->country_description }}</div>
					<div class="company-contact"><strong>Tel :</strong> {{ $row->loc_phone }} / <strong>Fax :</strong> {{ $row->loc_fax }}</div>
					<div class="company-contact"><strong>Email :</strong> {{ $row->loc_email }} / <strong>Web :</strong> {{ $row->loc_web }}</div>
					@endforeach
				</div>
			</div>

			@foreach ($headers as $header)
			<div class="col-md-12">
				<div class="title">PICK LIST - MRN #{{ $header->mrn_no }} / ISSUE #{{ $header->issue_id }} </div> 
			</div>

			<div class="col-md-12">
				<table class="table table-borderless"> 
				  
				  <tr>
				    <th width="6%">CO #</th>
				    <th width="1%">:</th>
				    <td width="26%">{{ $header->cust_order }}</td>
				    <th width="6%">Line</th>
				    <th width="1%">:</th>
				    <td width="26%">{{ $header->line_no }}</td>
				    <th width="6%">Style</th>
				    <th width="1%">:</th>
				    <td width="27%">{{ $header->style_no }}</td>
				  </tr>

				  <tr>
				    <th>PO #</th>
				    <th>:</th>
				    <td>{{ $header->po_nos }}</td>
				    <th>Cut Qty</th>
				    <th>:</th>
				    <td>{{ $header->cut_qty }}</td>
				    <th>Date</th>
				    <th>:</th>
				    <td>{{ date('d-M-Y H:i:s') }}</td>
				  </tr>

				  <tr>
				    <th>FNG #</th>
				    <th>:</th>
				    <td>{{ $header->fg_code }}</td>
				    <th></th>
				    <th></th>
				    <td></td>
				    <th></th>
				    <th></th>
				    <td></td>
				  </tr>

				</table>
			</div>
			@endforeach



			<div class="col-md-12">
				
				<table class="table table-striped table-bordered table-no-padding">
					<thead>
					  <tr>
					  	<th>&nbsp;#&nbsp;</th>
						<th>Code</th>
						<th>Description</th>
						<th>Sub&nbsp;Store</th>
						<th>Bin</th>
						<th>Box</th>
						<th>Size</th>
						<th>Requirement</th>
						<th>Issued&nbsp;Qty</th>
						<th>Balance</th>
                        <th colspan="6"></th>
					  </tr>
					</thead>
					
					<tbody>
					  @php 
					   	$c=1;
					  @endphp
					  @foreach ($details as $detail)
					  <!-- Row begin -->
					  <tr>
					  	<td rowspan="4" style="vertical-align:middle;text-align:center;">{{ $c }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->master_code }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->master_description }}</td>
						<td rowspan="4" style="vertical-align:middle;">{{ $detail->substore_name }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:center;">{{ $detail->store_bin_name }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:center;"></td>
						<td rowspan="4" style="vertical-align:middle;text-align:center;">{{ $detail->size_name }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ $detail->requested_qty }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ $detail->issue_qty }}</td>
						<td rowspan="4" style="vertical-align:middle;text-align:right;">{{ $detail->requested_qty-$detail->issue_qty }}</td>
					  </tr>

					  <tr>
					  	<td>Issued&nbsp;Qty</td>
					  	<td width="40"></td>
					  	<td width="40"></td>
					  	<td width="40"></td>
					  	<td width="40"></td>
					  	<td width="40"></td>
					  </tr>

					  <tr>
					  	<td>Signature</td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  </tr>

					  <tr>
					  	<td>Signature</td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  	<td></td>
					  </tr>
					  <!-- Row end -->
					  @php 
						$c++;
					  @endphp
					  @endforeach

					</tbody> 	

				</table>

		</div>
	</div>
</div>

@stop

