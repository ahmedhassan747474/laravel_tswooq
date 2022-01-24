@extends('admin.layout')


@section('content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1> {{ trans('labels.group') }} <small>...</small> </h1>
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
					<h4 class="mb-20">{{ __('labels.Edit') }}</h4>
					<div class="row">
						<div class="col-lg-12">
							{{-- <div class="alert alert-danger none errorarea">
								<ul id="errors">
	
								</ul>
							</div> --}}
							<form method="post" id="basicform" class="custom-form" action="{{ route('admin.group.update',$info->id) }}">
								@csrf
								@method('PUT')
								<div class="custom-form">
									<div class="form-group">
										<label for="name_en">{{ __('labels.Name') }} ({{ __('labels.English') }})</label>
										<input type="text" name="name_en" id="name_en" class="form-control input-rounded" placeholder="{{ __('Enter Group Name English') }}" required value="{{ $info->name_en }}">
									</div>
	
									<div class="form-group">
										<label for="name_ar">{{ __('labels.Name') }} ({{ __('labels.Arabic') }})</label>
										<input type="text" name="name_ar" id="name_ar" class="form-control input-rounded" placeholder="{{ __('Enter Group Name English') }}" required value="{{ $info->name_ar }}">
									</div>
	
									<button class="btn btn-success col-12 mt-15">{{ __('labels.Update') }}</button>
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
</script>
@endsection