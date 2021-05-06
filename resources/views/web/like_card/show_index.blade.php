@extends('web.layout')
@section('content')
 @php $r =   'web.like_card.index'; @endphp
 @include($r)
 @include('web.common.scripts.addToCompare')
@endsection
