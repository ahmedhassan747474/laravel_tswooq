@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.Suppliers') }} <small>{{ trans('labels.ListingAllSuppliers') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li class="active">{{ trans('labels.Suppliers') }}</li>
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
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-6 form-inline" id="contact-form">
                                    
                                    <div class="col-lg-4 form-inline" id="contact-form12"></div>
                                </div>
                                @if ($result['commonContent']['roles']->supplier_create == 1)
                                <div class="box-tools pull-right">
                                    <a href="{{ url('admin/suppliers/add')}}" type="button" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
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
                                            <th>{{ trans('labels.Name') }}</th>
                                            <th>{{ trans('labels.Phone') }}</th>
                                            <th>{{ trans('labels.Description') }} </th>
                                            <th>{{ trans('labels.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($suppliers['result']))
                                        @foreach ($suppliers['result'] as $key=>$listingSuppliers)
                                        <tr>
                                            <td><a href="{{ URL::to('admin/suppliersmainreport') . '?type=all&dateRange=&user_supplier_id=' . $listingSuppliers->id}}">{{ $listingSuppliers->id }}</a></td>
                                            <td><a href="{{ URL::to('admin/suppliersmainreport') . '?type=all&dateRange=&user_supplier_id=' . $listingSuppliers->id}}">{{ $listingSuppliers->name }}</a></td>
                                            <td>{{ $listingSuppliers->phone }}</td>
                                            <td style="text-transform: lowercase">{{ $listingSuppliers->description }}</td>
                                            <td>
                                                <ul class="nav table-nav">
                                                    <li class="dropdown">
                                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                            {{ trans('labels.Action') }} <span class="caret"></span>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            @if ($result['commonContent']['roles']->supplier_update == 1)
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('admin/suppliers/edit') }}/{{$listingSuppliers->id}}">{{ trans('labels.EditSuppliers') }}</a></li>
                                                            @endif
                                                            
                                                            <li role="presentation" class="divider"></li>
                                                            @if ($result['commonContent']['roles']->supplier_delete == 1)
                                                            <li role="presentation"><a data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.Delete') }}" id="deleteCustomerFrom"
                                                                users_id="{{ $listingSuppliers->id }}">{{ trans('labels.Delete') }}</a></li> 
                                                            @endif
                                                            
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4">{{ trans('labels.NoRecordFound') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                @if (count($suppliers['result']) > 0)
                                <div class="col-xs-12 text-right">
                                    {!! $suppliers['result']->appends(\Request::except('page'))->render() !!}
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
                        <h4 class="modal-title" id="deleteCustomerModalLabel">{{ trans('labels.Delete') }}</h4>
                    </div>
                    {!! Form::open(array('url' =>'admin/suppliers/delete', 'name'=>'deleteCustomer', 'id'=>'deleteCustomer', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                    {!! Form::hidden('action', 'delete', array('class'=>'form-control')) !!}
                    {!! Form::hidden('users_id', '', array('class'=>'form-control', 'id'=>'users_id')) !!}
                    <div class="modal-body">
                        <p>{{ trans('labels.DeleteSupplierText') }}</p>
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
