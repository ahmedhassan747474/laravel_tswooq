@extends('admin.layout')
@section('content')
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> {{ trans('labels.packges') }} <small>{{ trans('labels.listPackges') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active">{{ trans('labels.packges') }}</li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                    <form action="{{route('admin.updatePackges',$packges->id)}}" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" name="packge_id" value="{{$packges->id}}">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>Name</lable>
                                <input name="name" type="text" class="form-control" required="required" value="{{$packges->name}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>Price</lable>
                                <input name="price" type="number" class="form-control" required="required" value="{{$packges->price}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>Price قبل الخصم</lable>
                                <input name="price_discont" type="number" class="form-control" required="required" value="{{$packges->price_discont}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>Price 12</lable>
                                <input name="price12" type="number" class="form-control" required="required" value="{{$packges->price12}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>Price 12 قبل الخصم</lable>
                                <input name="price_discont_12" type="number" class="form-control" required="required" value="{{$packges->price_discont_12}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>{{trans('labels.Discount')}}</lable>
                                <input name="discount" type="number" class="form-control" value="{{$packges->discount}}">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <lable>{{trans('labels.Type')}}</lable>
                                <input name="type" type="text" class="form-control" required="required" value="{{$packges->type}}">
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                <lable>{{trans('labels.Description')}}</lable>
                                <textarea id="editor1" name="description" rows="6" class="form-control" required="required" value="">{{$packges->description}}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <input type="submit" class="btn btn-info" value="تحديث">
                        </div>  
                    </form>
                </div>
        </section>
</div>

<script src="{!! asset('admin/plugins/jQuery/jQuery-2.2.0.min.js') !!}"></script>
<script type="text/javascript">
    $(function() {

        //for multiple languages
        
        CKEDITOR.replace('editor1');

        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();

    });
</script>
@endsection