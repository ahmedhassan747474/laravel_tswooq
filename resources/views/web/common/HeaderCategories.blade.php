<?php

 function productCategoriesNav(){
  $categories = recursivecategories();
  if($categories){
    $parent_id = 0;
    $option = '';


      $option .= '<a class="menu-item" href="/shop?category=">'.trans("website.Choose Any Category").'</a>';
      foreach($categories as $parents){
        //   dd($categories);
        $option .= '<span class="dropdown">';
        $option .= '<a class="menu-item dropdown-toggle" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" href="/shop?category='.$parents->slug.'">'.$parents->categories_name.'</a>';
        if(isset($parents->childs)){
          $option .= '<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">';
          foreach ($parents->brands as $brand) {
            // dd($parents->brands);
            $option .= '<li><a class="dropdown-item" href="/shop?category='.$brand->slug.'" style="text-align: center;">' . $brand->categories_name . '</a></li>';

          }
          $option .= '</ul>';
        }
        $option .= '</span>';
      }
      $option .= '<a class="menu-item" href="/like_card_index">Like Card</a>';



    echo $option;
  }
 }

 function productCategories(){
  $categories = recursivecategories();
  if($categories){
    $parent_id = 0;
    $option = '';

      foreach($categories as $parents){
        if($parents->slug==app('request')->input('category')){
          $selected = "selected";
        }else {
          $selected = "";
        }
        ///shop?category=kia
        // $option .= '<a class="dropdown-item categories-list '.$selected.'" value="'.$parents->categories_name.'" slug="'.$parents->slug.'" '.$selected.'>'.$parents->categories_name.'</a>';

        // $option .= '<a class="dropdown-item categories-list" href="/shop?category='.$parents->categories_name.'">'.$parents->categories_name.'</a>';

        $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="'.$parents->slug.'" data-id="'.$parents->categories_id.'">'.$parents->categories_name.'</option>';


          // if(isset($parents->childs)){
          //   $i = 1;
          //   $option .= childcat($parents->childs, $i, $parent_id);
          // }

      }

    echo $option;
  }
 }

function productBrands(){
  $categories = recursivebrands();
    if($categories){
      $option = '';

      foreach($categories as $parents){

        if($parents->slug==app('request')->input('category')){
          $selected = "selected";
        }else {
          $selected = "";
        }

        // $option .= '<a class="dropdown-item categories-list '.$selected.'" value="'.$parents->categories_name.'" slug="'.$parents->slug.'" '.$selected.'>'.$parents->categories_name.'</a>';
        // $option .= '<a class="dropdown-item categories-list" href="/shop?category='.$parents->categories_name.'">'.$parents->categories_name.'</a>';
        $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="'.$parents->slug.'">'.$parents->categories_name.'</option>';
      }

    echo $option;
  }
}

function productPrices(){

  $option = '';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="0;1000"> 0  -  1000</option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=1000;2000"> 1000  -  2000</option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=2000;5000"> 2000  -  5000</option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=5000;10000"> 5000  -  10000</option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=10000;20000"> 10000  -  20000 </option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=20000;50000"> 20000  -  50000 </option>';

  $option .= '<option class="dropdown-item" style="background-color: #EEE;color: #000;" value="=50000;10000000000"> 50000  -   ??  </option>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=0%3B1000"> $0  -  $1000</a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=1000%3B2000"> $1000  -  $2000</a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=2000%3B5000"> $2000  -  $5000</a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=5000%3B10000"> $5000  -  $10000</a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=10000%3B20000"> $10000  -  $20000 </a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=20000%3B50000"> $20000  -  $50000 </a>';

  // $option .= '<a class="dropdown-item categories-list" href="/shop??min_price=0&max_price=2000&filters_applied=0&price=50000%3B10000000000"> $50000  -  $ ??  </a>';

  echo $option;
}

 function childcat($childs, $i, $parent_id){
  $contents = '';
  foreach($childs as $key => $child){
    $dash = '';
    for($j=1; $j<=$i; $j++){
        $dash .=  '&nbsp;&nbsp;';
    }

    if($child->slug==app('request')->input('category')){
      $selected = "selected";
    }else {
      $selected = "";
    }

    $contents.='<a class="dropdown-item categories-list '.$selected.'"  value="'.$child->categories_name.'" slug="'.$child->slug.'" '.$selected.'>'.$dash.$child->categories_name.'</a>';
    if(isset($child->childs)){

      $k = $i+1;
      $contents.= childcat($child->childs,$k,$parent_id);
    }
    elseif($i>0){
      $i=1;
    }

  }
  return $contents;
}


 function recursivecategories(){
  $items = DB::table('categories')
      ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
      ->select('categories.categories_id', 'categories.categories_slug as slug','categories_description.categories_name', 'categories.parent_id')
      ->where('categories_description.language_id','=', Session::get('language_id'))
      ->where('categories.categories_status','=', 1)
      ->get();

    foreach ($items as $item) {
      $brands = DB::table('categories')
        ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
        ->select('categories.categories_id', 'categories.categories_slug as slug','categories_description.categories_name', 'categories.parent_id')
        ->where('categories_description.language_id','=', Session::get('language_id'))
        ->where('categories.categories_status','=', 1)
        ->where('parent_id', '=', $item->categories_id)
        ->get();
      $item->brands = $brands;
    }

   if($items->isNotEmpty()){
      $childs = array();
      foreach($items as $item)
          $childs[$item->parent_id][] = $item;

      foreach($items as $item) if (isset($childs[$item->categories_id]))
          $item->childs = $childs[$item->categories_id];

      $tree = $childs[0];
      return  $tree;
    }
}

function recursivebrands(){
  $items = DB::table('categories')
      ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
      ->select('categories.categories_id', 'categories.categories_slug as slug','categories_description.categories_name', 'categories.parent_id')
      ->where('categories_description.language_id','=', Session::get('language_id'))
      ->where('categories.categories_status','=', 1)
      ->where('categories.parent_id', '>', 0)
      ->get();
  return  $items;
}


