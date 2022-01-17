@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.EditExpenses') }} <small>{{ trans('labels.EditCurrentExpenses') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li><a href="{{ URL::to('admin/expenses/display')}}"><i class="fa fa-users"></i> {{ trans('labels.ListingAllExpenses') }}</a></li>
            <li class="active">{{ trans('labels.EditExpenses') }}</li>
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
                        <h3 class="box-title">{{ trans('labels.EditExpenses') }} </h3>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box box-info">
                                    <!--<div class="box-header with-border">
                                          <h3 class="box-title">Edit category</h3>
                                        </div>-->
                                    <!-- /.box-header -->
                                    <br>
                                    @if (count($errors) > 0)
                                      @if($errors->any())
                                      <div class="alert alert-danger alert-dismissible" role="alert">
                                          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          {{$errors->first()}}
                                      </div>
                                      @endif
                                    @endif


                                    <!-- form start -->
                                    <div class="box-body">

                                        {!! Form::open(array('url' =>'admin/expenses/update', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                        {!! Form::hidden('expenses_id', $data['expenses']->id, array('class'=>'form-control', 'id'=>'id')) !!}

                                        <div class="form-group">
                                            <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Name') }} </label>
                                            <div class="col-sm-10 col-md-4">
                                              {!! Form::text('name', $data['expenses']->name , array('class'=>'form-control field-validate', 'id'=>'expenses_firstname')) !!}
                                              <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.NameText') }}</span>
                                              <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                            </div>
                                          </div>

                                          <div class="form-group">
                                            <label for="value" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Value') }} </label>
                                            <div class="col-sm-10 col-md-4">
                                              {!! Form::text('value',  $data['expenses']->value, array('class'=>'form-control field-validate', 'id'=>'value')) !!}
                                              <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.Value') }}</span>
                                              <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                            </div>
                                          </div>

                                          <div class="form-group">
                                            <label for="date" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Date') }} </label>
                                            <div class="col-sm-10 col-md-4">
                                              {!! Form::date('date',  $data['expenses']->date, array('class'=>'form-control field-validate', 'id'=>'date')) !!}
                                              <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.Date') }}</span>
                                              <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label for="date" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }} </label>
                                            <div class="col-sm-10 col-md-4">
                                            <input type="file" name="file" class="form-control" onchange="readURL(this);" />
                                            <img id="blah" src="#" alt="your image" />
                                            </div>
                                          </div>
                                         

                                        <!-- /.box-body -->
                                        <div class="box-footer text-center">
                                            <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }} </button>
                                            <a href="{{ URL::to('admin/expenses/display')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
                                        </div>
                                        <!-- /.box-footer -->
                                        {!! Form::close() !!}
                                    </div>
                                </div>
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

        <!-- Main row -->

        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
@endsection
