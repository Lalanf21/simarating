@extends('layout.master')
@section('title','Reservation history')

@push('after-style')
{{-- fullCalendar --}}
<link href="{{ asset('assets/plugins/fullcalendar/main.min.css') }}" rel="stylesheet" />
@endpush
{{-- custom style --}}
<style>
    .fc-event{
        cursor: pointer;
    }

    .fc-day-today {
        background-color: inherit !important;
    }
</style>
{{-- end custom style  --}}

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Reservation history</h4>
        <hr>
    </div>
</div>

{{-- kalender --}}
<div class="card">
    <div class="card-footer">
        <div class="container p-2">
            <div class="row">
                <div class="col-lg-12">
                    <div id="calendar">

                    </div>
                    <input type="hidden" name="date" id="date">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- /kalender --}}

<!-- modalQrCode -->
<div id="modalQrCode" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img class="qrcode" width="100%">
                <h5 class="text-center" id="qrCodeString"></h5>
            </div>
        </div>
    </div>
</div>
<!-- end modalQrCode -->

<!-- modal riwayat booking -->
<div id="modalRiwayat" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-footer bg-dark text-white">
                        <h4 class="text-center no_transaksi"></h4>
                    </div>
                    <div class="card-header">
                        <div class="container">
                            <div class="row p-3">
                                <div class="col-md-6">
                                    <strong><p>Company name</p></strong>
                                    <p class="nama_perusahaan"></p>
                                </div>
                                <div class="col-md-6">
                                    <strong><p>Reservation date</p></strong>
                                    <p class="tanggal"></p>
                                </div>
                            </div>
                            <div class="row p-3">
                                <div class="col-md-6">
                                    <strong><p>Name</p></strong>
                                    <div id="user_invited">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong><p>Number of seat</p></strong>
                                    <p class="seat"> </p>
                                </div>
                            </div>
        
                            <div class="row p-3">
                                <div class="col-md-6">
                                    <strong><p>Addon</p></strong>
                                    <div id="addons">
                                    
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <a href=""  data-bs-toggle="modal" data-bs-target="#modalQrCode">
                                        <img class="qrcode">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>
<!-- end modal riwayat booking -->

@endsection

@push('after-script')
{{-- fullCalendar --}}
<script src="{{ asset('assets/plugins/fullcalendar/main.min.js') }}"></script>

{{-- sweet alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    // calendar
    var allSchedule = @json($allSchedule);
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 450,
        events: allSchedule,
        longPressDelay: 100,
        selectLongPressDelay: 100,
        eventLongPressDelay: 100,
        eventClick:   function(arg){
            $.ajax({
                type 	: 'POST',
                url		: '{{route("search_booking")}}',
                headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                dataType: 'JSON',
                data 	: {
                    'tanggal'       :   arg.event.startStr,
                    'id'  :   arg.event.id,
                },
                success : function(rst) {
                    // assign variable header
                    const header = rst.header;
                    
                    // cek data header
                    if (header) {
                        // assign variabel jika data header ada
                        const user_invited = rst.userInvited;
                        const addon = rst.addon;
                        const qrString = rst.qrCode;
                        const seat = user_invited.length;
                        var tanggal = moment(header.booking_date).format('dddd, Do MMMM YYYY');
                        var dataUser = '';
                        var dataAddons = '';

                        // set detail header
                        $('h4.no_transaksi').text(header.transaksi_no);
                        $('p.nama_perusahaan').text(header.nama_perusahaan);
                        $('p.tanggal').text(tanggal);
                        $('p.seat').text(seat+' Seat');

                        // set user invited
                        if (user_invited.length > 0) {
                            $.each(user_invited,function(x,y){
                                dataUser += '<p>'+ y.nama_mitra+ ' - ' + y.email +'</p>';
                            });
                            $('#user_invited').html(dataUser);
                        }

                        // set addon
                        if (addons.length > 0) {
                            $.each(addons,function(x,y){
                                dataAddons += '<p>'+ y.nama_addon+ ' : ' + y.start_time+ ' - ' + y.end_time +'</p>';
                            });
                        }else{
                            dataAddons += '<p> No add-on </p>';
                        }
                        $('#addons').html(dataAddons);

                        // set qr code
                        const url = '{!! asset("/storage/img/qr-codes/'+qrString+'/qrcode.png") !!}';
                        $('img.qrcode').attr('src', url);
                        $('#qrCodeString').html(qrString);

                        // show modal
                        $('#modalRiwayat').modal('show');
                    }else{
                        Swal.fire({
                            title: "Information !",
                            icon: "warning",
                            text: "Data reservation not found"
                        });
                    }
                },
                error 	: function(xhr) {
                    read_error(xhr);
                },
                complete : function(xhr,status){
                    Pace.stop();  
                }
            });
            // end ajax
        },
    //   end event click object
    });
    // end full calendar init object

    // render full calendar
    calendar.render();
</script>
@endpush