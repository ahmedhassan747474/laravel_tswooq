{{-- <section class="new-products-content pro-content" >
  <div class="container">
    <div class="products-area">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> @lang('website.Top Selling of the Week')

            </h2>
            <p>
              @lang('website.Top Sellings Of the Week Detail')</p>
          </div>
        </div> 
      </div>
      <div class="row slick-ma">  
        @if($result['weeklySoldProducts']['success']==1)
        @foreach($result['weeklySoldProducts']['product_data'] as $key=>$products)
        @if($key==0)

          <div class="col-12-sm col-lg-2">

            <div class="product product-ad">
              <article>
                <div class="badges">
                  <?php
                  if(!empty($products->discount_price)){
                    $discount_price = $products->discount_price * session('currency_value');
                  }
                  $orignal_price = $products->products_price * session('currency_value');
            
                  if(!empty($products->discount_price)){
            
                  if(($orignal_price+0)>0){
                    $discounted_price = $orignal_price-$discount_price;
                    $discount_percentage = $discounted_price/$orignal_price*100;
                   }else{
                     $discount_percentage = 0;
                     $discounted_price = 0;
                  }
                  ?>
                  
                    <span class="badge badge-danger"  data-toggle="tooltip" data-placement="bottom" title="<?php echo (int)$discount_percentage; ?>% off"><?php echo (int)$discount_percentage; ?>%</span>
                    <?php }?>
                    @if($products->is_feature == 1)
                      <span class="badge badge-success">@lang('website.Featured')</span>                                            
                                
                  @else
                  <?php 
                  $current_date = date("Y-m-d", strtotime("now"));
            
                  $string = substr($products->products_date_added, 0, strpos($products->products_date_added, ' '));
                  $date=date_create($string);
                  date_add($date,date_interval_create_from_date_string($web_setting[20]->value." days"));
                  $after_date = date_format($date,"Y-m-d");
                  if($after_date>=$current_date){
                    print '<span class="badge badge-info">';
                    print __('website.New');
                    print '</span>';
                  }
                  ?> 
                  @endif   
                </div>
                <div class="detail">
                  <span class="tag">
                    <?php 
                    $cates = '';
                    foreach($products->categories as $key=>$category){
                      $cates = trim($category->categories_name);
                    } 
                    echo $cates ;                    
                    ?>                               
                </span>
                <h5 class="title"><a href="{{ URL::to('/product-detail/'.$products->products_slug)}}">{{$products->products_name}}</a></h5>
                <p class="discription">
                   <?php
                      $descriptions = strip_tags($products->products_description);
                      echo stripslashes($descriptions);
                    ?>
                </p>
                      
                <div class="price">                                    
                  @if(!empty($products->discount_price))
                    {{Session::get('symbol_left')}}&nbsp;{{$discount_price+0}}&nbsp;{{Session::get('symbol_right')}}<span>{{Session::get('symbol_left')}}{{$orignal_price+0}}{{Session::get('symbol_right')}}</span>
                  @else
                    {{Session::get('symbol_left')}}&nbsp;{{$orignal_price+0}}&nbsp;{{Session::get('symbol_right')}}
                  @endif   
                </div>  
               
              <div class="pro-sub-buttons">
                  <div class="buttons">
                      <button type="button" class="btn  btn-link is_liked" products_id="<?=$products->products_id?>" data-toggle="tooltip" data-placement="bottom" title="@lang('website.Add to Wishlist')"><i class="fas fa-heart"></i>@lang('website.Add to Wishlist')</button>
                      <button type="button" class="btn btn-link" onclick="myFunction3({{$products->products_id}})" data-toggle="tooltip" data-placement="bottom" title="@lang('website.Add to Compare')"><i class="fas fa-align-right" ></i>@lang('website.Add to Compare')</button>
                  </div>
                  </div>          
             
                 </div>
                <picture>
                    <div class="product-hover">
                    
                        @if($products->products_type==0)
                          @if(!in_array($products->products_id,$result['cartArray']))
                              @if($products->defaultStock==0)
                                  <button type="button"  class="btn btn-block btn-danger swipe-to-top" products_id="{{$products->products_id}}">@lang('website.Out of Stock')</button>
                              @elseif($products->products_min_order>1)
                                <a class="btn btn-block btn-secondary swipe-to-top" href="{{ URL::to('/product-detail/'.$products->products_slug)}}">@lang('website.View Detail')</a>
                              @else
                                  <button type="button" class="btn btn-block btn-secondary cart swipe-to-top" products_id="{{$products->products_id}}">@lang('website.Add to Cart')</button>
                              @endif
                          @else
                              <button type="button" class="btn btn-block btn-secondary active swipe-to-top">@lang('website.Added')</button>
                          @endif
                      @elseif($products->products_type==1)
                          <a class="btn btn-block btn-secondary swipe-to-top" href="{{ URL::to('/product-detail/'.$products->products_slug)}}">@lang('website.View Detail')</a>
                      @elseif($products->products_type==2)
                          <a href="{{$products->products_url}}" target="_blank" class="btn btn-block btn-secondary swipe-to-top">@lang('website.External Link')</a>
                      @endif

                    </div>
                  <img class="img-fluid" src="{{asset('').$products->image_path}}" alt="{{$products->products_name}}">
                </picture>
              
    
                 
              </article>
            </div>
          </div> 
          
          @endif
          @endforeach
          @endif
          @if($result['weeklySoldProducts']['success']==1)
            @foreach($result['weeklySoldProducts']['product_data'] as $key=>$products)
            @if($key!=0)
            @if($key<=6)

          <div class="col-12 col-sm-6 col-lg-2">
            @include('web.common.product')
          </div>  
          @endif
          @endif
          @endforeach
          @endif
   
      </div>
    </div>

    <div class="mobiles-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> جوالات
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',1)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="games-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> العاب
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',7)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="tablet-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> تابلت
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',8)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="xx-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> اكسسورات
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',10)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="fashion-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> ازياء
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',32)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="sms-card-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> شرائح بيانات
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',56)->take(20)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="offers-product">
        <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="pro-heading-title">
            <h2> العروض
            </h2>
          </div>
        </div> 
      </div>
        <div class="row slick-ma justify-content-center">
            @foreach(\App\Models\Web\ProductCategory::whereHas('product')->where('categories_id',66)->take(10)->get() as $p)
            <div class="col-lg-2 mb-3 mt-3">
                @if($p->product)
                    @if($p->product->image)
                        <img style="width:200px;margin:auto;" class="card-img-top" src="{{asset($p->product->image->image_category->path)}}" alt="Card image cap">
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>
  </div>  
  @section('scripts')
    <script>
      $('.slick-ma').slick({
         arrows:true,
        dots: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        prevArrow : '<span class="prev-arrow"><i class="fa fa-angle-left"></i></span>',
        nextArrow : '<span class="next-arrow"><i class="fa fa-angle-right"></i></span>',
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              arrows:true,
              dots: true
            }
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
          // You can unslick at a given breakpoint now by adding:
          // settings: "unslick"
          // instead of a settings object
        ]
});
    </script>
  @stop
</section>
 --}}
