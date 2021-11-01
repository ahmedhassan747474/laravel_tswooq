<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Admin Panel | </title>
{{--{{ $pageTitle }}--}}
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="Themescoder" content="">
  <link rel="stylesheet" href="{{ asset('admin/css/vendors.css') }}">

	<link rel="stylesheet" href="{{ asset('admin/css/aiz-core.css') }}">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

  
  <!-- Bootstrap 3.3.6 -->
  <link href="{!! asset('admin/bootstrap/css/bootstrap.min.css') !!}" media="all" rel="stylesheet" type="text/css" />
  <link href="{!! asset('admin/bootstrap/css/styles.css') !!} " media="all" rel="stylesheet" type="text/css" />
  <link href="{!! asset('admin/css/dropzone.css') !!}" media="all" rel="stylesheet" type="text/css" />
  <link href="{!! asset('admin/css/custom.ilyas.css') !!}" media="all" rel="stylesheet" type="text/css" />
  {{--fancybox--}}

  <link href="{!! asset('admin/css/jquery.fancybox.min.css') !!}" media="all" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />

  <!-- Select2 -->
  {{-- <link rel="stylesheet" href="{!! asset('admin/plugins/select2/select2.min.css') !!} "> --}}

    <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{!! asset('admin/plugins/colorpicker/bootstrap-colorpicker.min.css') !!} ">
    <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{!! asset('admin/plugins/timepicker/bootstrap-timepicker.min.css') !!} ">
  <!-- Ionicons -->
  <link href="{!! asset('admin/css/ionicons.min.css') !!}" media="all" rel="stylesheet" type="text/css" />
  <link href="{!! asset('admin/css/image-picker.css') !!}" media="all" rel="stylesheet" type="text/css" />
  <!-- daterange picker -->
  <link rel="stylesheet" href="{!! asset('admin/plugins/daterangepicker/daterangepicker-bs3.css') !!} ">
   <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{!! asset('admin/plugins/datepicker/datepicker3.css') !!} ">
  <!-- jvectormap -->
  <link href="{!! asset('admin/plugins/jvectormap/jquery-jvectormap-1.2.2.css') !!} " media="all" rel="stylesheet" type="text/css" />
  <!-- Theme style -->
  <link href="{!! asset('admin/dist/css/AdminLTE.min.css')  !!} " media="all" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link href="{!! asset('admin/dist/css/skins/_all-skins.min.css') !!} " media="all" rel="stylesheet" type="text/css" />
    <!-- iCheck for checkboxes and radio inputs -->
    <link href="{!! asset('admin/plugins/iCheck/all.css')  !!} " media="all" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
      window.csrf_token = "{{ csrf_token() }}"
    </script>

  <!-- Ionicons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" media="all" rel="stylesheet" type="text/css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="../../assets/js/jquery-2.2.4.min"><\/script>')</script>

  
  <![endif]-->


  <script>
    var AIZ = AIZ || {};
      AIZ.local = {
          nothing_found: '{{ trans('Nothing found') }}',
          choose_file: '{{ trans('Choose file') }}',
          file_selected: '{{ trans('File selected') }}',
          files_selected: '{{ trans('Files selected') }}',
          add_more_files: '{{ trans('Add more files') }}',
          adding_more_files: '{{ trans('Adding more files') }}',
          drop_files_here_paste_or: '{{ trans('Drop files here, paste or') }}',
          browse: '{{ trans('Browse') }}',
          upload_complete: '{{ trans('Upload complete') }}',
          upload_paused: '{{ trans('Upload paused') }}',
          resume_upload: '{{ trans('Resume upload') }}',
          pause_upload: '{{ trans('Pause upload') }}',
          retry_upload: '{{ trans('Retry upload') }}',
          cancel_upload: '{{ trans('Cancel upload') }}',
          uploading: '{{ trans('Uploading') }}',
          processing: '{{ trans('Processing') }}',
          complete: '{{ trans('Complete') }}',
          file: '{{ trans('File') }}',
          files: '{{ trans('Files') }}',
      }
</script>

</head>
<style>
.dragable-box-cursor img{
  cursor: move;
}

.select2-container .select2-selection--multiple{
  min-height: 150px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice{
  color: black;
}
</style>
@yield('style')
