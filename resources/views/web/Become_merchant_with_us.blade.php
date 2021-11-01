@extends('web.layout')
@push('styles')
    <style>
        ul{
            list-style: none;
        }
    </style>
@endpush
@section('content')

       <!-- End Header Content -->

       <!-- NOTIFICATION CONTENT -->
         @include('web.common.notifications')
      <!-- END NOTIFICATION CONTENT -->
      <div class="container mb-5 mt-5">
        <div class="pricing card-deck flex-column flex-md-row mb-3">
    @if($packges)
        @foreach($packges as $packge)
            <div class="card card-pricing text-center px-3 mb-4">
                <span style="background-color:@if($packge->type == 'diamond') #99154E; @elseif($packge->type == 'golden') #091353; @else #FFC93C; @endif color:#FFF;width: 100%;font-size: 25px;font-weight: bold;" class="h6 w-60 mx-auto px-4 py-1 rounded-bottom shadow-sm">{{$packge->name}}</span>
                <div class="bg-transparent card-header pt-4 border-0">
                    {{-- <h2 style="text-decoration: line-through" class="h1 font-weight-normal text-center mb-0" data-pricing-value="15">{{$packge->price}}<span class="price">SAR</span><span class="h6 text-muted ml-2"></span></h2> --}}
                        <h1 class="h1 font-weight-normal text-center mb-0" data-pricing-value="15">{{$packge->price }}<span class="price">SAR</span><span class="h6 text-muted ml-2"></span></h1>
                </div>
                    <div class="card-body pt-0">
                @if($packge->type === 'diamond')
                    {{-- <ul class="list-unstyled mb-4"> --}}
                        {{-- <li>Up 	متجر اونلاين</li>
                        <li>نقاط بيع نظام متكامل لادارة مبيعاتك</li>
                        <li>شركة شحن وشركة شحن أونلاين</li>
                        <li>	افضل خطط التسويق الإلكتروني</li>
                        <li>	استهداف العملاء المحتملين بطرق متميزة</li>
                        <li>	إدارة السوشيال ميديا كاملة</li>
                        <li>	إضافة منتجات نشاطك التجارى للموقع الإلكتروني</li>
                        <li>انشاء تصميمات مبدعة ومتميزة لتجارتك</li>
                        <li>تقديم افكار تسويقية متميزة تساعد في جذب العملاء</li>
                        <li>	العمل على محركات البحث SEO</li>
                        <li>	تطبيق اندرويد وتطبيق IOS</li>
                        <li>  نسبه من الارباح 0.5%</li> --}}
                        {!! $packge->description !!}
                    {{-- </ul> --}}
                @elseif($packge->type === 'golden')
                    {{-- <ul class="list-unstyled mb-4">
                        <li>	متجر اونلاين</li>
                        <li>نقاط بيع نظام متكامل لادارة مبيعاتك</li>
                        <li>شركة شحن وشركة شحن أونلاين</li>
                        <li>إضافة منتجات نشاطك التجارى للموقع الإلكتروني</li>
                        <li>	تطبيق اندرويد وتطبيق IOS</li>
                        <li> نسبة من الارباح 1%</li>
                        
                    </ul> --}}

                    {!! $packge->description !!}

                @elseif($packge->type === 'silver')
                    {{-- <ul class="list-unstyled mb-4">
                        <li>	متجر اونلاين</li>
                        <li>شركة شحن وشركة شحن أونلاين</li>
                        <li>تطبيق اندرويد وتطبيق IOS</li>
                        <li>نسبة من الارباح 1.5%</li>
                    </ul> --}}
                    {!! $packge->description !!}
                @endif
                <a href="{{route('getPackge',$packge->type)}}" class="btn btn-outline-secondary mb-3">{{trans('website.booking')}}</a>
            </div>
        </div>
        @endforeach
    @endif
    </div>
</div>
@include('web.common.scripts.addToCompare')
@include('web.common.scripts.Like')
@endsection
