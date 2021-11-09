@extends('web.layout')
@section('content')
<div class="container">
    <div class="row">
    @if(count($product) > 0)
        @foreach($product as $key=>$pro)
            <div class="col-lg-4 mb-3 mt-3">
                <div class="card" style="">
                    @if($pro->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($pro->image->image_category->path)}}" alt="Card image cap">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{$pro->products_slug}}</h5>
                        <h6>{{$pro->price_buy}}</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        
                    </div>
                </div>
            </div>
        @endforeach
        @else
        <div class="container">
            <p class="mt-3 alert alert-danger">لا يوجد منتجات فى هذا المتجر</p>
        </div>
    @endif
    </div>
        
</div>


@stop