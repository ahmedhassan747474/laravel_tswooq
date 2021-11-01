@extends('web.layout')
@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-6">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">1 شهر</h5>
                    <p class="card-text">SAR {{$package->price}}</p>
                    <a href="{{route('packg.checkout',['id'=>$package->id,'month'=>1])}}" class="btn btn-primary">{{trans('website.booking')}}</a>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">12 شهر</h5>
                    <span class="card-text" style="text-decoration: line-through">SAR {{$package->price * 12}}</span>
                    <p class="card-text">SAR {{($package->price-$package->discount)* 12}}</p>
                    {{-- <p class="card-text">SAR {{$package->price * 12}}</p> --}}
                    @php
                        // $price=($package->price-$package->discount)* 12;
                    @endphp
                    <a href="{{route('packg.checkout',['id'=>$package->id,'month'=>12])}}" class="btn btn-outline-secondary mb-3">{{trans('website.booking')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>





@endsection