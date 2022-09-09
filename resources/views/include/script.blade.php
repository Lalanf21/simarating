 <!-- JS Files-->
 <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
 <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
 <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
 <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
 <script src="{{ asset('assets/js/moment.js') }}"></script>
 <!--plugins-->
 <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
 <script src="{{ asset('assets/plugins/notifications/js/lobibox.min.js') }}"></script>
 {{-- <script src="{{ asset('assets/plugins/notifications/js/notifications.min.js') }}"></script> --}}
 
 <!-- Main JS-->
 <script src="{{ asset('assets/js/main.js') }}"></script>
 {{-- Custom JS --}}
 <script>
    @if(Session::has('error_message'))
        notifMessage('{!! Session::get('error_message') !!}','error');
    @endif

    @if(Session::has('success_message'))
        notifMessage('{!! Session::get('success_message') !!}','success');
    @endif

    @if($errors->any())
        var div = '';
            @foreach ($errors->all() as $error)
                div +='<div>{{ $error }}</div>';
            @endforeach
        notifMessage(div,'error');
    @endif
</script>
