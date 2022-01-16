@extends('admin.layout')
@section('content')
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1> {{ trans('labels.shippingrates') }} <small>{{ trans('labels.shippingrates') }}...</small> </h1>
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
		
		  <div class="col-lg-4">      
			<div class="card">
				<div class="card-body">
					<form id="basicform" method="post" action="{{ route('admin.group.store') }}">
						@csrf
						<div class="custom-form">
							<div class="form-group">
								<label for="name_en">{{ __('Name') }} ({{ __('English') }}) </label>
								<input type="text" name="name_en" class="form-control" id="name_en" placeholder="{{ __('Group Name English') }}">
							</div>
	
							<div class="form-group">
								<label for="name_ar">{{ __('Name') }} ({{ __('Arabic') }}) </label>
								<input type="text" name="name_ar" class="form-control" id="name_ar" placeholder="{{ __('Group Name Arabic') }}">
							</div>
	
							<div class="form-group mt-20">
								<button class="btn btn-primary col-12" type="submit">{{ __('Add New Group') }}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-lg-8" >      
			<div class="card">
				<div class="card-body">
					@php
					if (!empty($req)) {
						$groups=App\Group::where('vendor_id',auth()->user()->id)->where('name_en','LIKE','%'.$req.'%' )->orWhere('name_ar','LIKE','%'.$req.'%' )->latest()->paginate(12);	
					}
					else{
						if(auth()->user()->role_id==1){
							$ids=DB::table('users')->where('role_id',1)->pluck('id');
							// dd($ids);
							$groups=App\Group::whereIn('vendor_id',$ids)->latest()->paginate(12);
						}else{
							$groups=App\Group::where('vendor_id',auth()->user()->id)->latest()->paginate(12);
						}
					}
					@endphp
					<div class="table-responsive">
						<div class="card-action-filter">
							<form> 
							<input type="text" class="form-control" name="query" required>
							</form>
							<form id="basicform1" method="post" action="{{ route('admin.group.destroy') }}">
								@csrf
								<div class="card-filter-content">
									<div class="single-filter">
										<div class="form-group">
											<select class="form-control" name="method">
												<option value="delete">{{ __('Delete Permanently') }}</option>
											</select>
										</div>
									</div>
									<div class="single-filter">
										<button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
									</div>
								</div>
							</div>
							<table class="table category">
								<thead>
									<tr>
										<th class="am-select">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
												<label class="custom-control-label" for="checkAll"></label>
											</div>
										</th>
										<th class="am-title">{{ __('Title') }}</th>
	
									</tr>
								</thead>
								<tbody>
									@foreach($groups as $group)
									<tr>
										<th>
											<div class="custom-control custom-checkbox">
												<input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $group->id }}" value="{{ $group->id }}">
												<label class="custom-control-label" for="customCheck{{ $group->id }}"></label>
											</div>
										</th>
										<td>
											{{ $group->name_en }}
											<div class="hover">
												<a href="{{ route('admin.group.edit',$group->id) }}">{{ __('Edit') }}</a>
												<a href="{{ route('admin.group.product.edit',$group->id) }}">{{ __('Products') }}</a>
	
											</div>
										</td>
									</tr>
									@endforeach
	
								</tbody>
							</form>	
							<tfoot>
								<tr>
									<th class="am-select">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
											<label class="custom-control-label" for="checkAll"></label>
										</div>
									</th>
									<th class="am-title">{{ __('Title') }}</th>
								</tr>
							</tfoot>
						</table>
						<div class="f-right">{{ $groups->links() }}</div>
					</div>
					
				</div>
			</div>
		</div>
		  <!-- /.box -->
		</div>
		<!-- /.col -->
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
<script type="text/javascript">
	"use strict";
	function success(res){
		location.reload();
	}

	$(".checkAll").on('click',function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
})(jQuery);	
</script>
@endsection