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
            <div class="box-header">
                <div class="col-lg-6 form-inline" id="contact-form">
                    <div class="col-lg-4 form-inline" id="contact-form12"></div>
                </div>
                <div class="box-tools pull-right">
                    <a href="{{ URL::to('admin/packges/add') }}" type="button" class="btn btn-block btn-primary">{{ trans('labels.AddNew') }}</a>
                </div>
                </br>
            </div>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{trans('labels.packge_name')}}</th>
                            <th scope="col">{{trans('labels.packge_price')}}</th>
                            <th scope="col">{{trans('labels.packge_type')}}</th>
                            <th scope="col">{{trans('labels.actions')}}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if($packges)
                                @foreach($packges as $packges)
                                    <tr>
                                    <th scope="row">{{$packges->id}}</th>
                                    <td>{{$packges->name}}</td>
                                    <td>{{$packges->price}}</td>
                                    <td>{{$packges->type}}</td>
                                    <td>
                                        <a href="{{route('admin.editPackge',$packges->id)}}" class="btn btn-info">{{trans('labels.edit')}}</a>
                                        <a href="{{route('admin.deletePackges',$packges->id)}}" class="pid btn btn-danger">{{trans('labels.delete')}}</a>
                                    </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        </table>
                </div>
            </div>

        </section>
        <script>
            let pid =  document.querySelectorAll('.pid');
                pid.forEach(ele=>{
                    ele.onclick = function(){
                        return confirm('Are You Sure ?');
                    }
                });
        </script>
</div>



@endsection