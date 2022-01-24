@extends('admin.layout')
@push('styles_aiz')


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"  />
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    
@endpush
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.shops') }} <small>{{ trans('labels.shops') }}...</small> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li class="active">{{ trans('labels.shops') }}</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Info boxes -->

    <!-- /.row -->

    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('labels.shops') }} </h3>
            {{-- <div class="box-tools pull-right">
            	<a href="{{ URL::to('admin/addadmins')}}" type="button" class="btn btn-block btn-primary">{{ trans('labels.addadmins') }}</a>
            </div> --}}
          </div>

          <!-- /.box-header -->
          <div class="box-body">
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
              <div class="col-xs-12">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>{{ trans('labels.ID') }}</th>
                      <th>{{ trans('labels.Full Name') }}</th>
                      <th>{{ trans('labels.Email') }}</th>
                      <th>{{ trans('labels.ShopName') }}</th>
                      <th>صوره المحل</th>
                      <th>{{ trans('labels.status_show') }}</th>
                      {{-- <th>{{ trans('labels.Action') }}</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                   @if (count($result['admins']) > 0)
						@foreach ($result['admins']  as $key=>$admin)
							<tr>
								<td>{{ $admin->id }}</td>

                <td>
                  {{ $admin->first_name }} {{ $admin->last_name }}
                </td>
                <td>
                   {{ $admin->email }}
                </td>

                <td>
                  {{ $admin->shop_name != null ? $admin->shop_name : '---------' }}
               </td>
               <td>
                @if(isset($admin->shopImg)) 
                @php
                // dd($admin);
                  $imagepath= \App\ImageCategories::where('image_id',$admin->avatar)->where(function($q){
                      $q->where('image_type', '=', 'THUMBNAIL')
                                              ->where('image_type', '!=', 'THUMBNAIL')
                                              ->orWhere('image_type', '=', 'ACTUAL');
                  })->first()->path ??'';
                //  echo asset($imagepath);
                @endphp
                  <img style="width: 121px;height: 106px;" src='{{asset($imagepath)??''}}'>
                @else 
                shop Img not found
                @endif
               </td>

              </td>
                                {{-- <td>
                                @if($admin->user_types_id==1)
                                	<strong class="badge bg-green">
                                @else
                                	<strong class="badge bg-light-blue">
                                @endif
                                	{{$admin->user_types_name}}</strong>
                                </td> --}}
                              

                                <td>
                                  <input data-id="{{$admin->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="{{trans('labels.Active')}}" data-off="{{trans('labels.Inactive')}}" {{ $admin->status_show ? 'checked' : '' }}>
                               </td>
                                {{-- <td>
                                  @if($admin->isActive==1)
                                    <strong class="badge bg-green">{{trans('labels.Active')}} </strong>
                               	  @else
                                	<strong class="badge bg-light-grey">{{trans('labels.InActive')}} </strong>
                                  @endif

                                </td> --}}
								              {{-- <td>
                                <ul class="nav table-nav">
                              <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                  {{ trans('labels.Action') }} <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="editadmin/{{ $admin->id }}">{{ trans('labels.editadmin') }}</a></li>
                                    <li role="presentation" class="divider"></li>
                                    <li role="presentation"><a data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.Delete') }}" id="deleteCustomerFrom" customers_id="{{ $admin->id }}">{{ trans('labels.Delete') }}</a></li>
                                </ul>
                              </li>
                            </ul>
								</td> --}}
							</tr>
						@endforeach
                    @else
                    	<tr>
							<td colspan="5">{{ trans('labels.NoRecordFound') }}</td>
						</tr>
                    @endif
                  </tbody>
                </table>
                @if (count($result['admins']) > 0)
					<div class="col-xs-12 text-right">
						{{$result['admins']->links()}}
					</div>
                 @endif
              </div>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>

    <!-- /.row -->

    <!-- deleteCustomerModal -->
	<div class="modal fade" id="deleteCustomerModal" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="deleteCustomerModalLabel">{{ trans('labels.deleteAdmin') }}</h4>
		  </div>
		  {!! Form::open(array('url' =>'admin/deleteadmin', 'name'=>'deleteAdmin', 'id'=>'deleteAdmin', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
				  {!! Form::hidden('action',  'delete', array('class'=>'form-control')) !!}
				  {!! Form::hidden('id',  '', array('class'=>'form-control', 'id'=>'customers_id')) !!}
		  <div class="modal-body">
			  <p>{{ trans('labels.Are you sure you want to delete this admin') }}</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('labels.Close') }}</button>
			<button type="submit" class="btn btn-primary">{{ trans('labels.Delete') }}</button>
		  </div>
		  {!! Form::close() !!}
		</div>
	  </div>
	</div>

    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content notificationContent">

		</div>
	  </div>
	</div>

    <!-- Main row -->

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" ></script> --}}
<script>
  $(function() {
    $('.toggle-class').change(function() {
        var status_show = $(this).prop('checked') == true ? 1 : 0; 
        var user_id = $(this).data('id'); 
         
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/admin/shops/changeStatus',
            data: {'status_show': status_show, 'user_id': user_id},
            success: function(data){
              // console.log(data.success)
              swal("تم بنجاح!", data.success, "success");
            }
        });
    })
  })
</script>
@endpush