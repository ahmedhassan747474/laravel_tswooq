@extends('admin.layout')
@section('content')
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> {{ trans('labels.packge_orders') }} <small>{{ trans('labels.listPackgesRequests') }}...</small> </h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li class="active">{{ trans('labels.packge_orders') }}</li>
            </ol>
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
                            <th scope="col">{{trans('labels.packgeRequestUserName')}}</th>
                            <th scope="col">{{trans('labels.packgeRequestUserPhone')}}</th>
                            <th scope="col">{{trans('labels.packgeRequestUserEmail')}}</th>
                            <th scope="col">{{trans('labels.month')}}</th>
                            {{-- <th scope="col">{{trans('labels.actions')}}</th> --}}

                            </tr>
                        </thead>
                        <tbody>
                        @if($packgesRequest)
                            @foreach($packgesRequest as $packgesRequest)
                                <tr>
                                    <th scope="row">{{$packgesRequest->id}}</th>
                                    <td>{{$packgesRequest->packge->name}}</td>
                                    <td>{{$packgesRequest->packge->price * $packgesRequest->month -($packgesRequest->month == 12 ? $packgesRequest->discound : 0 )}}</td>
                                    <td>{{$packgesRequest->user->first_name. ' '. $packgesRequest->user->last_name}}</td>
                                    <td>{{$packgesRequest->user->phone}}</td>
                                    <td>{{$packgesRequest->user->email}}</td>
                                    <td>{{$packgesRequest->month}}</td>
                                    {{-- <td>
                                        <a href="#" class="btn btn-info">{{trans('labels.edit')}}</a>
                                        <a href="#" class="pid btn btn-danger">{{trans('labels.delete')}}</a>
                                    </td> --}}
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