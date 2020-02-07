@extends('reports.common.template')

@section('title')
    BOM Report
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
				<div class="title">BOM {{ $header->bom_id }} (COST ID - {{ $header->costing_id }})</div> 
			</div>

			<div class="col-md-12">
				<table class="table table-borderless"> 
				  <tr>
				    <th width="10%">SC</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->sc_no }}</td>
				    <th width="1%">Status</th>
				    <th width="1%">:</th>
				    <td width="22%">{{ $header->status }}</td>
				    <th width="10%">FNG</th>
				    <th width="1%">:</th>
				    <td width="23%">{{ $header->master_code }}</td>
				  </tr>

				  <tr>
				    <th>Style</th>
				    <th>:</th>
				    <td>{{ $header->style_no }}</td>
				    <th>Country</th>
				    <th>:</th>
				    <td>{{ $header->country_description }}</td>				   
				    <th>BOM Stage</th>
				    <th>:</th>
				    <td>{{ $header->bom_stage_description }}</td>
				  </tr>

				  <tr>
				    <th>Season</th>
				    <th>:</th>
				    <td>{{ $header->season_name }}</td>
				    <th>Color Type</th>
				    <th>:</th>
				    <td>{{ $header->color_option }}</td>				   
				    <th>Created By</th>
				    <th>:</th>
				    <td>{{ $header->user_name }}</td>
				  </tr>
				</table>
			</div>

			<div class="col-md-12">
				<table class="table table-bordered details">
					<tr class="green-row">
						<th width="20%">Total SMV</th>
						<th width="20%">Finance Charges</th>
						<th width="20%">FOB</th>
						<th width="20%">EPM</th>
						<th width="20%">NP Margin</th>					
					</tr>

					<tr>
						<td>{{ $header->total_smv }}</td>
						<td>{{ $header->finance_charges }}</td>
						<td>{{ $header->fob }}</td>
						<td>{{ $header->epm }}</td>
						<td>{{ $header->np_margin }}</td>
					</tr>

					<tr class="green-row">
						<th>Finance Cost</th>
						<th>Fabric Cost</th>
						<th>Elastic Cost</th>
						<th>Trim Cost</th>
						<th>Packing Cost</th>
					</tr>

					<tr>
						<td>{{ $header->finance_cost }}</td>
						<td>{{ $header->fabric_cost }}</td>
						<td>{{ $header->elastic_cost }}</td>
						<td>{{ $header->trim_cost }}</td>
						<td>{{ $header->packing_cost }}</td>
					</tr>

					<tr class="green-row">
						<th>Other Cost</th>
						<th>Total RM Cost</th>
						<th>Labour Cost</th>					
						<th>Coperate Cost</th>
						<th>Total Cost</th>
					</tr>

					<tr>
						<td>{{ $header->other_cost }}</td>
						<td>{{ $header->total_rm_cost }}</td>
						<td>{{ $header->labour_cost }}</td>
						<td>{{ $header->coperate_cost }}</td>
						<td>{{ $header->total_cost }}</td>						
					</tr>		
				
				</table>						
			</div>
			@endforeach
			

			<div class="col-md-12">
				<table class="table table-striped table-bordered table-no-padding">
				  <thead>
				    <tr>
				      <th width="20px">#</th>
				      <th>Item Description</th>
				      <th>Comp</th>
				      <th>Origin</th>
				      <th>Unit</th>
				      <th>Net Con</th>
				      <th>Gross&nbsp;Con</th>
				      <th>Wastage</th>
				      <th>Freight</th>
				      <th>Surcharge</th>
				      <th>UP</th>
				      <th>TC PC</th>
				    </tr>
				  </thead>
				  <tbody>

				  	@foreach($categories as $category)
					  <tr>
					    <td colspan="12" class="main-category">{{ $category->category_name }}</td>
					  </tr>

					  @php 
						$c=1;
						$cat_sum=0;
					  @endphp

					  @foreach($details as $detail)
					  	
					  	@if($category->category_id==$detail->category_id)
					  	<tr>
					      <td class="text-center">{{ $c }}</td>
					      <td>{{ $detail->master_description }}</td>
					      <td>{{ $detail->product_component_description }}</td>
					      <td>{{ $detail->origin_type }}</td>
					      <td>{{ $detail->uom_code }}</td>
					      <td class="text-right">{{ $detail->net_consumption }}</td>
					      <td class="text-right">{{ $detail->gross_consumption }}</td>
					      <td class="text-right">{{ $detail->wastage }}%</td>
					      <td class="text-right">{{ $detail->freight_charges }}</td>
					      <td class="text-right">{{ $detail->surcharge }}</td>
					      <td class="text-right">{{ $detail->bom_unit_price }}</td>
					      <td class="text-right">{{ $detail->total_cost }}</td>
					    </tr>
					    @php
							$cat_sum += $detail->total_cost;
						@endphp
					    @endif

					  @endforeach

					  @php 
						$c++;
					  @endphp

					  	<tr>
					      <td colspan="11" class="cat-total">TOTAL {{ $category->category_name }} COST</td>
					      <td class="text-bold cat-total text-right bottom-border">{{ number_format($cat_sum, 4, '.', '') }}</td>					   
					    </tr>

					@endforeach

					@foreach ($headers as $header)

						<tr>
							<td colspan="12">&nbsp;</td>
						</tr>
					    <tr >
					      <td colspan="11" class="cat-total" style="background-color: #e6e6e6;">Total RM Cost</td>
					      <td class="text-right cat-total bottom-border" style="background-color: #e6e6e6;">{{ $header->total_rm_cost }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11">Labour / Sub Contracting Cost</td>
					      <td class="text-right">{{ $header->labour_cost }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11" class="cat-total" style="background-color: #e6e6e6;">Total Manufacturing Cost</td>
					      <td class="text-right cat-total bottom-border" style="background-color: #e6e6e6;">{{ $header->total_rm_cost+$header->labour_cost }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11">Finance Cost</td>
					      <td class="text-right">{{ $header->finance_cost }}</td>					   
					    </tr>			    
					    <tr>
					      <td colspan="11">Corporate Cost</td>
					      <td class="text-right">{{ $header->coperate_cost }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11" class="cat-total" style="background-color: #e6e6e6;">Total Cost</td>
					      <td class="text-right cat-total bottom-border" style="background-color: #e6e6e6;">{{ $header->total_cost }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11">Total FOB</td>
					      <td class="text-right">{{ $header->fob }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11">SMV</td>
					      <td class="text-right">{{ $header->total_smv }}</td>					   
					    </tr>
					    <tr>
					      <td colspan="11">EPM</td>
					      <td class="text-right">{{ $header->epm }}</td>					   
					    </tr>	    	 
					@endforeach



				  </tbody>
				</table>	
			</div>



		</div>
	</div>
</div>

@stop

