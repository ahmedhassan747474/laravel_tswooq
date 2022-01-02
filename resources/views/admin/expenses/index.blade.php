@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.Expenses') }} <small>{{ trans('labels.ListingAllExpenses') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li class="active">{{ trans('labels.Expenses') }}</li>
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
                                    <form name='registration' id="registration" class="registration" method="get" action="">
                                        <input type="hidden" value="{{csrf_token()}}">
                                        <div class="input-group-form search-panel ">
                                            {{-- <select type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" name="FilterBy" id="FilterBy">
                                                <option value="" selected disabled hidden>Filter By</option>
                                                <option value="Name" @if(isset($filter)) @if ($filter=="Name" ) {{ 'selected' }} @endif @endif>{{ trans('labels.Name') }}</option>
                                                <option value="E-mail" @if(isset($filter)) @if ($filter=="E-mail" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Email') }}</option>
                                                <option value="Phone" @if(isset($filter)) @if ($filter=="Phone" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Phone') }}</option>
                                                <option value="Address" @if(isset($filter)) @if ($filter=="Address" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Address') }}</option>
                                                <!-- <option value="Suburb" @if(isset($filter)) @if ($filter=="Suburb" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Suburb') }}</option> -->
                                                <option value="Postcode" @if(isset($filter)) @if ($filter=="Postcode" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Postcode') }}</option>
                                                <option value="City" @if(isset($filter)) @if ($filter=="City" ) {{ 'selected' }}@endif @endif>{{ trans('labels.City') }}</option>
                                                <option value="State" @if(isset($filter)) @if ($filter=="State" ) {{ 'selected' }}@endif @endif>{{ trans('labels.State') }}</option>
                                                <option value="Country" @if(isset($filter)) @if ($filter=="Country" ) {{ 'selected' }}@endif @endif>{{ trans('labels.Country') }}</option>
                                            </select> --}}
                                            <input type="text" class="form-control input-group-form " name="parameter" placeholder="{{ trans('labels.Search') }}" id="parameter" @if(isset($parameter)) value="{{$parameter}}" @endif>
                                            <button class="btn btn-primary " id="submit" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                            @if(isset($parameter,$filter)) <a class="btn btn-danger " href="{{url('admin/expenses/display')}}"><i class="fa fa-ban" aria-hidden="true"></i> </a>@endif
                                        </div>
                                    </form>
                                    <div class="col-lg-4 form-inline" id="contact-form12"></div>
                                </div>
                                <div class="box-tools pull-right">
                                    <a href="{{ url('admin/expenses/add')}}" type="button" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                                </div>
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
                                            <th>{{trans('labels.ID') }}</th>
                                            <th>{{trans('labels.Name') }}</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($expenses['result']))
                                        @php
                                            $total=0;
                                        @endphp
                                        @foreach ($expenses['result'] as $key=>$listingExpenses)
                                        <tr>
                                            <td>{{ $listingExpenses->id }}</td>                                           
                                            <td>{{ $listingExpenses->name }}</td>                                           
                                            <td>{{ $listingExpenses->value }}</td>                                           
                                            <td>{{ $listingExpenses->image }}</td>                                           
                                            <td>{{ $listingExpenses->date }}</td>                                           
                                            <td>
                                                <ul class="nav table-nav">
                                                    <li class="dropdown">
                                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                            {{ trans('labels.Action') }} <span class="caret"></span>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{url('admin/expenses/edit') }}/{{$listingExpenses->id}}">{{ trans('labels.EditExpenses') }}</a></li>
                                                            <li role="presentation" class="divider"></li>
                                                            <li role="presentation"><a data-toggle="tooltip" data-placement="bottom" title="{{ trans('labels.Delete') }}" id="deleteExpenseFrom"
                                                                  users_id="{{ $listingExpenses->id }}">{{ trans('labels.Delete') }}</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        @php
                                            $total +=$listingExpenses->value;
                                        @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan="2">{{ trans('labels.Total') }}</td>
                                            <td>{{ $total }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="4">{{ trans('labels.NoRecordFound') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                @if (count($expenses['result']) > 0)
                                <div class="col-xs-12 text-right">
                                    {{--'vendor.pagination.default'--}}
                                    {{--{{$expenses['result']->links()}}--}}
                                    {!! $expenses['result']->appends(\Request::except('page'))->render() !!}
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

        <!-- deleteExpenseModal -->
        <div class="modal fade" id="deleteExpenseModal" tabindex="-1" role="dialog" aria-labelledby="deleteExpenseModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="deleteExpenseModalLabel">{{ trans('labels.Delete') }}</h4>
                    </div>
                    {!! Form::open(array('url' =>'admin/expenses/delete', 'name'=>'deleteExpense', 'id'=>'deleteExpense', 'method'=>'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) !!}
                    {!! Form::hidden('action', 'delete', array('class'=>'form-control')) !!}
                    {!! Form::hidden('users_id', '', array('class'=>'form-control', 'id'=>'users_id')) !!}
                    <div class="modal-body">
                        <p>{{ trans('labels.DeleteExpenseText') }}</p>
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
