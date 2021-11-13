
@extends('web.layout')
@section('content')
<div class="container">
    <div class="row">
       
   {{-- @if($result['special']['success']==1 or $result['top_seller']['success']==1 or $result['most_liked']['success']==1 )

<section class="tabs-content pro-content" >
  <div class="container">
    <div class="products-area">
       <div class="row justify-content-center">
         <div class="col-12 col-lg-6">
           <!--<div class="pro-heading-title">
             <h2>  @lang('website.WELCOME TO STORE')
             </h2>
             <p> 
               @lang('website.WELCOME TO STORE DETAIL')
              </p>
             </div><-->
           </div>
       </div>
    
    </div>
  </div>
  <div class="tabs-main">
    <div class="container">
      <div class="row">
         <div class="col-md-12 p-0">
             <div class="nav" role="tablist" id="tabCarousel">
               
                 @if($result['top_seller']['success']==1)
                  <a class="nav-link btn  active show" data-toggle="tab" href="#featured" role="tab" ><span data-toggle="tooltip" data-placement="bottom" title="@lang('website.TopSales')">@lang('website.TopSales')</span></a>
                 @endif
                 @if($result['special']['success']==1)
                  <a class="nav-link btn"  data-toggle="tab" href="#special" role="tab" aria-controls="special" aria-selected="true" ><span data-toggle="tooltip" data-placement="bottom" title="@lang('website.Special')">@lang('website.Special')</span></a> 
                 @endif
                 @if($result['most_liked']['success']==1)
                 <a class="nav-link btn" data-toggle="tab"  href="#liked" role="tab" aria-controls="liked" aria-selected="true" ><span data-toggle="tooltip" data-placement="bottom" title="@lang('website.MostLiked')">@lang('website.MostLiked')</span></a> 
                 @endif

                </div> 
           <!-- Tab panes -->
           <div class="tab-content">

            @if($result['top_seller']['success']==1)

            <div role="tabpanel" class="tab-pane fade active show" id="featured">
              <div class="tab-carousel-js">
                  

                    @foreach($result['top_seller']['product_data'] as $key=>$products)
                    <div class="slick ">
                      @include('web.common.product')
                    </div> 
                    @endforeach

                    <div class="">
                      <div class="product">
                        <article>
                         <div class="btn-all">
                          <a href="{{url('/shop?type=topseller')}}" class="btn btn-secondary swipe-to-top">@lang('website.View All')</a>
                         </div>                              
                        </article>
                      </div>
                    </div>

              </div>
            <!-- 1st tab --> 
          </div>


            @endif

            @if($result['special']['success']==1) 
            
            <div role="tabpanel" class="tab-pane fade" id="special">
              <div class="tab-carousel-js ">
                  

                    @foreach($result['special']['product_data'] as $key=>$products)
                    <div class="slick ">
                      @include('web.common.product')
                    </div>
                    @endforeach

                    <div class="">
                      <div class="product">
                        <article>
                         <div class="btn-all">
                          <a href="{{url('/shop?type=special')}}" class="btn btn-secondary swipe-to-top">@lang('website.View All')</a>
                         </div>                              
                        </article>
                      </div>
                    </div>                  

              </div>
            <!-- 2nd tab --> 
          </div>

          
            @endif


            @if($result['most_liked']['success']==1)
             <div role="tabpanel" class="tab-pane fade" id="liked">
                 <div class="tab-carousel-js">
                     
                     
                      @foreach($result['most_liked']['product_data'] as $key=>$products)
                      <div class="slick">
                        @include('web.common.product')
                      </div> 
                      @endforeach
                     <div class="">
                      <div class="product">
                        <article>
                         <div class="btn-all">
                          <a href="{{url('/shop?type=mostliked')}}" class="btn btn-secondary swipe-to-top">@lang('website.View All')</a>
                         </div>                              
                        </article>
                      </div>
                    </div>

               <!-- 3rd tab -->
             </div>
             
            @endif
             
           </div>
         </div>
      </div>
    </div>
 
 </div>  
</section>

@endif --}}

@push('styles')
<style>
  .bodyrtl .slick-slider .slick-slide {
    width: 240px!important;
  }
  </style>
    
@endpush

<div class="row justify-content-center" style="width: 100%;position: relative;right: 0%;">
    <div class="col-12 col-lg-6">
     <div class="pro-heading-title text-center">
      @if($result['user']->avatari)
      <img style="width:110px;margin:auto;" class="card-img-top" src="{{asset('').$result['user']->avatari->image_category->path}}" alt="Card image cap">
      @endif

       <h2> {{ $result['user']->shop_name }}
       </h2>
       {{-- @php
        $image = DB
         dd($result['user']);
       @endphp --}}
       {{-- <img src="{{ asset(''). }}" --}}
       
     </div>
    </div>
</div>
shop
@if($result['groups'] !=null)
@foreach ($result['groups'] as $index=>$item)
<section class="tabs-content pro-content" >
  <div class="container">
    <div class="products-area">
       <div class="row justify-content-center">
         <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> {{ $item->name_ar }}
            </h2>
            
          </div>
         </div>
       </div>
    
    </div>
  </div>
  <div class="tabs-main">
    <div class="container">
      <div class="row">
         <div class="col-md-12 p-0">
             {{-- <div class="nav" role="tablist" id="tabCarousel">
               @php
                 $counter=0;
               @endphp
              @foreach ($result['groups'] as $index=>$item)
              @if($item->products!=[])
              <a class="nav-link btn  {{ $counter==0 ? 'active show' : '' }}" data-toggle="tab" href="#g{{ $item->id }}" role="tab" ><span data-toggle="tooltip" data-placement="bottom" title="{{ $item->name_ar}}">{{ $item->name_ar }}</span></a>     
              @php
                $counter++;
              @endphp
              @endif
              @endforeach
                

                </div>  --}}
           <!-- Tab panes -->
           <div class="tab-content">
             @php
               $counter=0;
             @endphp

            
            @if($item->products)
            <div role="tabpanel" class="tab-pane fade {{ $counter==0 ? 'active show' : 'active show' }}" id="g{{ $item->id }}">
                <div class="tab-carousel-js">
                    @php
                    $counter++;
                    @endphp
                      @foreach($item->products as $key=>$products)
                      <div class="slick " >
                        @include('web.common.product')
                      </div> 
                      @endforeach

                      <div class="">
                        <div class="product">
                          <article>
                          <div class="btn-all">
                            <a href="{{url('/shop?type=topseller')}}" class="btn btn-secondary swipe-to-top">@lang('website.View All')</a>
                          </div>                              
                          </article>
                        </div>
                      </div>
                </div>
              <!-- 1st tab --> 
            </div>

            @endif        


           
             
           </div>
         </div>
      </div>
    </div>
 </div>  
</section>
@endforeach

@endif
        {{-- <div class="container">
            <p class="mt-3 alert alert-danger">لا يوجد مجموعات فى هذا المتجر</p>
        </div>
    @endif
    </div> --}}
        
</div>


@stop