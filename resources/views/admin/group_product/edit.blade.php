@extends('admin.layout')

@section('content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1> {{ __('Edit Group Product') }}  </h1>
	  {{-- <ol class="breadcrumb">
		<li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
		<li class="active"> {{ trans('labels.shippingrates') }}</li>
	  </ol> --}}
	</section>
  
	<!--  content -->
	<section class="content">
	  <!-- Info boxes -->
  
	  <!-- /.row -->
  
	  <div class="row">
		<div class="col-xs-12">
			@if (count($errors) > 0)
			@if($errors->any())
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				{{$errors->first()}}
			</div>
			@endif
			@endif
		</div>
	  </div>
	  <div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<h4 class="mb-20">{{ __('Edit Group Product') }}</h4>
					<div class="row">
						<div class="col-lg-12">
							{{-- <div class="alert alert-danger none errorarea">
								<ul id="errors">
	
								</ul>
							</div> --}}
							<form method="post" id="basicform" class="custom-form" action="{{ route('admin.group.product.update',$info->id) }}">
								@csrf
								@method('PUT')
								<div class="custom-form">
									<div class="form-group">
										{{-- @php
											dd($items[0]->descriptions);
											@endphp --}}
										<label for="exampleFormControlSelect1">{{ __('Select Products') }}</label>
										<select class="form-control js-example-basic-multiple" name="items[]" id="exampleFormControlSelect1" multiple="multiple" style="width:100%">
											@foreach($items as $item)
											
											  <option value="{{$item->products_id}}" {{$item->is_selected == 1 ? 'selected' : ''}}>({{$item->products_slug ? $item->products_slug : 'Not Exist'}} ) - {{ $item->descriptions[0]->products_name??'' }} - {{$item->rest_name}}</option>
											  @endforeach
										</select>
										
									</div>
									<button class="btn btn-success col-12 mt-15">{{ __('Update') }}</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	  <!-- /.row -->
		  <!-- deletetaxRateModal -->
		 
  
	  <!--  row -->
  
	  <!-- /.row -->
	</section>
	<!-- /.content -->
  </div>


@endsection



@section('script')
{{-- <script src="{{ asset('admin/js/form.js') }}"></script> --}}
<script>
	"use strict";	
	function success(res){
		location.reload();
	}
	function errosresponse(xhr){
		$("#errors").html("<li class='text-danger'>"+xhr.responseJSON[0]+"</li>")
	}

	$(document).ready(function() {
	    $('.js-example-basic-multiple').select2();
	});
</script>
@endsection