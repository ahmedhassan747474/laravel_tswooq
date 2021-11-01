<!DOCTYPE html>
<html>

<!-- meta contains meta taga, css and fontawesome icons etc -->
@include('admin.common.meta')
<!-- ./end of meta -->


<body class=" hold-transition skin-blue sidebar-mini">
<!-- wrapper -->
<div class="wrapper">

    {{-- <div class="se-pre-con" id="loader" style="/* display: none; */">
        <div class="pre-loader">
          <div class="la-line-scale">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
          </div>
          <p>@lang('labels.Loading')..</p>
        </div>
     
      </div> --}}

    <!-- header contains top navbar -->
@include('admin.common.header')
<!-- ./end of header -->

    <!-- left sidebar -->
@include('admin.common.sidebar')
<!-- ./end of left sidebar -->

    <!-- dynamic content -->
@yield('content')
<!-- ./end of dynamic content -->

    <!-- right sidebar -->
@include('admin.common.controlsidebar')
<!-- ./right sidebar -->
    @include('admin.common.footer')
</div>
<!-- ./wrapper -->

<!-- The actual snackbar -->
<div id="snackbar">{{ trans('labels.Cache Cleared Successfully') }}</div>

<!-- all js scripts including custom js -->
@include('admin.common.scripts')
<!-- ./end of js scripts -->

@stack('scripts')

    @yield('script')

    <script type="text/javascript">
	    @foreach (session('flash_notification', collect())->toArray() as $message)
	        AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
	    @endforeach


        if ($('#lang-change').length > 0) {
            $('#lang-change .dropdown-menu a').each(function() {
                $(this).on('click', function(e){
                    e.preventDefault();
                    var $this = $(this);
                    var locale = $this.data('flag');
                    $.post('{{ route('language.change') }}',{_token:'{{ csrf_token() }}', locale:locale}, function(data){
                        location.reload();
                    });

                });
            });
        }
        function menuSearch(){
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i,item){
				if($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#'){
					return item;
				}
			});

			if(filter !== ''){
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if(items.length > 0){
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						 $("#search-menu").append(`<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				}else{
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block">{{ trans('Nothing Found') }}</span></li>`);
				}
			}else{
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
        }
    </script>

</body>
</html>
