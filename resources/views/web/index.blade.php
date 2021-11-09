@extends('web.layout')
@section('content')
       <!-- End Header Content -->

       <!-- NOTIFICATION CONTENT -->
         @include('web.common.notifications')
      <!-- END NOTIFICATION CONTENT -->

       <!-- Carousel Content -->
       <?php  echo $final_theme['carousel']; ?>
       <!-- Fixed Carousel Content -->

      <!-- Banners Content -->
      <!-- Products content -->
      
       @include('web.product-sections.tab')
      
     
@include('web.common.scripts.addToCompare')
@include('web.common.scripts.Like')
@endsection
