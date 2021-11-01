@extends('web.layout')
@section('content')
       <!-- End Header Content -->
       <!-- NOTIFICATION CONTENT -->
         @include('web.common.notifications')
      <!-- END NOTIFICATION CONTENT -->
      <div class = "shops">
        <div class="container">
          <div class="row">
			@foreach($users as $user)
				<div class="col-sm-6 col-lg-4 mt-3">
					<div class="card">
                        @if($user->avatari)
						    <img style="width:200px;margin:auto;" class="card-img-top" src="{{$user->avatari->image_category->path}}" alt="Card image cap">
                        @endif
						<div class="card-body">
						<h5 class="card-title">{{$user->shop_name}}</h5>
						<a class="btn btn-primary" href="{{route('shop.products',$user->id)}}" class="btn btn-info">{{trans('website.product')}}</a>
						<a class="btn btn-primary" href="{{route('shop.groups',$user->id)}}" class="btn btn-info">{{trans('website.groups')}}</a>
					</div>
				</div>
				</div>
			@endforeach
          </div>          
        </div>
      </div>
@include('web.common.scripts.addToCompare')
@include('web.common.scripts.Like')
@endsection
