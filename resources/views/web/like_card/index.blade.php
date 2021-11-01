  <!-- Shop Page One content -->
  <div class="container-fuild">
    <nav aria-label="breadcrumb">
        <div class="container">
            <ol class="breadcrumb">
              @if(!empty($result['category_name']) and !empty($result['sub_category_name']))
              <li class="breadcrumb-item"><a href="{{ URL::to('/')}}">@lang('website.Home')</a></li>
              <li  class="breadcrumb-item"><a href="{{ URL::to('/like_card')}}">Like Card</a></li>
             <li  class="breadcrumb-item"><a href="{{ URL::to('/like_card?category='.$result['category_slug'])}}">{{$result['category_name']}}</a></li>
             <li  class="breadcrumb-item active">{{$result['sub_category_name']}}</li>
             @elseif(!empty($result['category_name']) and empty($result['sub_category_name']))
             <li class="breadcrumb-item active">{{$result['category_name']}}</li>
             @else
             <li class="breadcrumb-item"><a href="{{ URL::to('/')}}">@lang('website.Home')</a></li>
             <li class="breadcrumb-item active">Like Card</li>
             @endif
            </ol>
        </div>
      </nav>
  </div> 
  
   
      
    <section class="pro-content">
          <div class="container">
              <div class="page-heading-title">
                  <h2> Like Card </h2>
              </div>
          </div>
          
          <section class="shop-content shop-two">
                  
              <div class="container">
                <div class="row">

                  <div class="col-12 col-lg-3  d-lg-block d-xl-block right-menu"> 
                    <div class="right-menu-categories">
                      @if($result['categories']->response == 1)
                        @foreach($result['categories']->data as $category)
                        @if(count($category->childs) > 0) 
                        <a class="main-manu collapsed" href="#like_card_{{$category->id}}" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="men-cloth"> 
                        @else
                        <a class=" main-manu" href="{{route('like_card')}}?category_id={{$category->id}}"> 
                        @endif
                          <img class="img-fuild" src="{{$category->amazonImage}}"> {{$category->categoryName}}
                        </a>
                        @if(count($category->childs) > 0)
                        <div class="sub-manu multi-collapse collapse" id="like_card_{{$category->id}}" style="">
                            <ul class="unorder-list">
                              @foreach($category->childs as $sub_category)
                                @if(count($sub_category->childs) > 0)
                                  <a class="main-manu collapsed" href="#like_card_{{$sub_category->id}}" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="men-cloth"> 
                                  <img class="img-fuild" src="{{$sub_category->amazonImage}}"> {{$sub_category->categoryName}}
                                  </a>
                                      <div class="sub-manu multi-collapse collapse" id="like_card_{{$sub_category->id}}" style="">
                                        <ul class="unorder-list">
                                          @foreach($sub_category->childs as $sub_sub_category)
                                            <li class="list-item">
                                                <a class="list-link" href="{{route('like_card')}}?category_id={{$sub_sub_category->id}}"> &nbsp;&nbsp;
                                                    <i class="fa fa-angle-right"></i>{{$sub_sub_category->categoryName}}
                                                </a>
                                            </li>
                                          @endforeach
                                        </ul>
                                    </div>
                                @else
                                <li class="list-item">
                                    <a class="list-link" href="{{route('like_card')}}?category_id={{$sub_category->id}}"> &nbsp;&nbsp;
                                        <i class="fa fa-angle-right"></i>{{$sub_category->categoryName}}
                                    </a>
                                </li>
                                @endif
                              @endforeach
                            </ul>
                        </div>
                        @endif
                        @endforeach
                      @else
                      <a class=" main-manu" href="#"> 
                        Not Available
                      </a>
                      @endif
                    </div>
                  </div>

                  <div class="col-12 col-lg-9">
                      <div class="products-area"> 
                        <section id="swap" class="shop-content" >
                          <div class="products-area">
                            <div class="row">  
                              @if($result['products']->response == 1)
                                  @foreach($result['products']->data as $product)    
                                  <div class="col-12 col-lg-4 col-sm-6 griding">
                                    @include('web.common.card')
                                  </div>
                                  @endforeach
                                @else
                                <div class="col-12 col-lg-4 col-sm-6 griding">
                                <br>
                                <h3>@lang('website.No Record Found!')</h3></div>
                                @endif                  
                            </div>
                          </div> 
                        </section>  
                      </div>
                  </div>
                  
                </div>
              </div>
          </section> 
     
    </section>
    
   </section>
  
  
  