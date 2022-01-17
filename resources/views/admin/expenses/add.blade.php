@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.AddExpense') }} <small>{{ trans('labels.AddNEWExpense') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li><a href="{{ URL::to('admin/expenses/display')}}"><i class="fa fa-users"></i> {{ trans('labels.ListingAllExpenses') }}</a></li>
            <li class="active">{{ trans('labels.AddExpense') }}</li>
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
                        <h3 class="box-title">{{ trans('labels.AddExpense') }} </h3>
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
                                    @if (session('update'))
                                    <div class="alert alert-success alert-dismissable custom-success-box" style="margin: 15px;">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        <strong> {{ session('update') }} </strong>
                                    </div>
                                    @endif

                                    @if (count($errors) > 0)
                                    @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        {{$errors->first()}}
                                    </div>
                                    @endif
                                    @endif

                                    <div class="box-body">
                                        {!! Form::open(array('url' =>'admin/expenses/add', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}

                                              <div class="form-group">
                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Name') }} </label>
                                                <div class="col-sm-10 col-md-4">
                                                  {!! Form::text('name',  '', array('class'=>'form-control field-validate', 'id'=>'expenses_firstname')) !!}
                                                  <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.NameText') }}</span>
                                                  <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                              </div>

                                              <div class="form-group">
                                                <label for="value" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Value') }} </label>
                                                <div class="col-sm-10 col-md-4">
                                                  {!! Form::text('value',  '', array('class'=>'form-control field-validate', 'id'=>'value')) !!}
                                                  <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.Value') }}</span>
                                                  <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                              </div>

                                              <div class="form-group">
                                                <label for="date" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Date') }} </label>
                                                <div class="col-sm-10 col-md-4">
                                                  {!! Form::date('date',  '', array('class'=>'form-control field-validate', 'id'=>'date')) !!}
                                                  <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.Date') }}</span>
                                                  <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                </div>
                                              </div>

                                              <div class="form-group">
                                                <label for="date" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }} </label>
                                                <div class="col-sm-10 col-md-4">
                                                <input type="file" name="file" class="form-control" onchange="readURL(this);" />
                                                <img id="blah" src="#" alt="" />

                                                </div>
                                              </div>
                                             
                                              {{-- <div class="col-xs-12 col-md-6 ">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="Modalmanufactured" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" id="closemodal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                        <h3 class="modal-title text-primary" id="myModalLabel">{{ trans('labels.Choose Image') }}</h3>
                                                                    </div>
                                                                    <div class="modal-body manufacturer-image-embed">
                                                                        @if(isset($allimage))
                                                                        <select class="image-picker show-html " name="image_id" id="select_img">
                                                                            <option value=""></option>
                                                                            @foreach($allimage as $key=>$image)
                                                                            <option data-img-src="{{asset($image->path)}}" class="imagedetail" data-img-alt="{{$key}}" value="{{$image->id}}"> {{$image->id}} </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @endif
                                                                    </div>
                                                                    <div class="modal-footer">

                                                                        <a href="{{url('admin/media/add')}}" target="_blank" class="btn btn-primary pull-left">{{ trans('labels.Add Image') }}</a>
                                                                        <button type="button" class="btn btn-default refresh-image"><i class="fa fa-refresh"></i></button>
                                                                        <button type="button" class="btn btn-primary" id="selected" data-dismiss="modal">Done</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div id="imageselected">
                                                          {!! Form::button( trans('labels.Add Image'), array('id'=>'newImage','class'=>"btn btn-primary ", 'data-toggle'=>"modal", 'data-target'=>"#Modalmanufactured" )) !!}
                                                          <br>
                                                          <div id="selectedthumbnail" class="selectedthumbnail col-md-5"> </div>
                                                          <div class="closimage">
                                                              <button type="button" class="close pull-left image-close " id="image-close"
                                                                style="display: none; position: absolute;left: 105px; top: 54px; background-color: black; color: white; opacity: 2.2; " aria-label="Close">
                                                                  <span aria-hidden="true">&times;</span>
                                                              </button>
                                                          </div>
                                                        </div>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.UploadProductImageText') }}</span>

                                                    </div>
                                                </div>
                                            </div> --}}


                                          
                                              <div class="box-footer text-center">
                                                  <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }}</button>
                                                  <a href="{{ URL::to('admin/expenses/display')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
                                              </div>

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
