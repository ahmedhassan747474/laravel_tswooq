<?php

 function shopCategories(){
    $categories = shopRecursivecategories();
    if($categories){
    $parent_id = 0;
    $option = '';
    // dd($categories);
      foreach($categories as $parents){
        $parent_slug  = $parents->slug;    

        $hasChild = "href=".url('shop?category=').$parents->slug;;

        $option .= '
        <a class=" main-manu"'. $hasChild .'> <img class="img-fuild" src="'.asset($parents->path).'"> '.$parents->categories_name.'</a>';
      }

    echo $option;
  }
}


 function shopRecursivecategories(){
  $items = DB::table('categories')
      ->leftJoin('categories_description','categories_description.categories_id', '=', 'categories.categories_id')
      ->leftJoin('image_categories', 'categories.categories_icon', '=', 'image_categories.image_id')
      ->select('categories.categories_id', 'categories.categories_slug as slug', 'image_categories.path as path', 'categories_description.categories_name', 'categories.parent_id', 'categories.categories_status')
      ->where('categories_description.language_id','=', Session::get('language_id'))
      ->where('categories.categories_status','=', 1)
      ->groupBy('categories.categories_id')
      ->get();
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

 ?>
