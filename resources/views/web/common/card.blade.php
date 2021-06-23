<div class="product" style="padding-top: 0;padding-bottom: 30px;">
    <article>
      <div class="thumb">
        <div class="badges">
          <?php
            $orignal_price = $product->productPrice;
          ?>  
        </div>

        <div class="mobile-icons d-lg-none d-xl-none">
          <div class="icons">
            <div class="icon-liked"> 
  
              <a class="icon active swipe-to-top is_liked" products_id="<?=$product->productId?>">
                <i class="fas fa-heart"></i>
              </a>
  
            </div>
  
            <div class="icon modal_show " products_id ="{{$product->productId}}">
              <i class="fas fa-eye"></i>
            </div>
            <a onclick="myFunction3({{$product->productId}})" class="icon">
              <i class="fas fa-align-right" data-fa-transform="rotate-90"></i>
            </a>
          </div>
        </div>
        <img class="img-fluid" src="{{$product->productImage}}" alt="{{$product->productName}}">
      </div>
      
      
        <div class="content" onclick="window.open('https://wa.me/+966536990111?text=id%20%3D%3E%20{{$product->productId}}%0Aname%20%3D%3E%20{{$product->productName}}%0Aimage%20%3D%3E%20{{$product->productImage}}%0Aprice%20%3D%3E%20{{$orignal_price+0}}%20{{$product->productCurrency}}', '_blank')">
            <h5 class="title text-center"><a target="_blank" href="https://wa.me/+966536990111?text=id%20%3D%3E%20{{$product->productId}}%0Aname%20%3D%3E%20{{$product->productName}}%0Aimage%20%3D%3E%20{{$product->productImage}}%0Aprice%20%3D%3E%20{{$orignal_price+0}}%20{{$product->productCurrency}}">{{$product->productName}}</a></h5>
            <div class="price">                     
                {{Session::get('symbol_left')}}&nbsp;{{$orignal_price+0}}&nbsp;{{$product->productCurrency}}
            </div>  
          </a>
        </div>                 
    </article>
  </div>