@extends('layout.master')
@section('title','Reservation')
@section('content')
@push('after-style')
{{-- fullCalendar --}}
<link href="{{ asset('assets/plugins/fullcalendar/main.min.css') }}" rel="stylesheet" />

{{-- select2 --}}
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endpush
<style>
    .fc-day-today {
        background-color: inherit !important;
    }
    
    .fc-day-past{ 
        /* background-color : #f3f3f3f3; */
        /* border: solid red 0.5px !important; */
        cursor:no-drop !important;
    }

    .fc-event{
        cursor: pointer;
    }

    .fc-day{
        cursor: pointer;
    }

    .fc-daygrid-event-harness{
        padding: 5px;
    }
</style>

<div class="card">
    <div class="card-header">
      <h3>
          Choose reservation date
      </h3>
    </div>
</div>

{{-- kalender --}}
<div class="card">
    <div class="card-header">
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

{{-- modal add --}}
<div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-center tanggalSelected">

            </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
           
            <form action="{{ route('confirmation-booking') }}" method="post" id="formBooking">
                @csrf
                @method('POST')
                <div class="container">
                    <input type="hidden" name="tanggal_booking" class="form-control" id="bookingDate">

                    <div class="col-12">
                        <label for="jumlah_seat" class="form-label"> Number of seat </label>
                        <input type="number" name="jumlah_seat" class="form-control  @error('jumlah_seat') is-invalid @enderror" id="jumlah_seat"  min="1">
                        @error('jumlah_seat')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-12 mt-2">
                        <label for="shift">Shift</label> <br>
                        <div class="row" id="shift">
                            <div class="col-md-6">
                                <input type="radio" class="form-check-input" id="shift1" name="shift" value="1">
                                <label class="form-check-label text-info" for="shift1">
                                    <span class="badge bg-info text-black">
                                        Morning (08:00 - 13:00)
                                    </span> 
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="form-check-input" id="shift2" name="shift" value="2"> 
                                <label class="form-check-label text-warning" for="shift2">
                                    <span class="badge bg-warning text-black">
                                        Afternoon (13:00 - 18:00)
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-none" id="inviteRow">
                        <div class="col-12" >
                            <div class="row">
                                <label for="invite" class="form-label mt-3 "> Invite </label>
                                <select name="invite[]" id="invite" class="form-select @error('invite') is-invalid @enderror" multiple="multiple">
                                    @foreach ($partner as $data)
                                        <option value="{{ $data->id }}">
                                            {{ $data->nama.' - '. $data->mitra->nama_perusahaan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('invite')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
    
                        <div class="col-12 mt-3 d-flex justify-content-end">
                            <div class="form-check form-switch">
                                <label class="form-check-label" for="isAddon">Add-on ?</label>
                                <input class="form-check-input" name="isAddon" type="checkbox" value="1" id="isAddon">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row d-none" id="rowAddon">
                        <div class="row addonForm" id="addonForm1" data-addon="1">
                            <div class="col-12">
                                <label for="addon" class="form-label mt-3"> addon </label>
                                <select name="addon[]" id="addon" class="form-select">
                                    <option value="" disabled selected>Select</option>
                                    @foreach ($addon as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="col-12">
                                <label for="start_time" class="form-label mt-3 "> Start hour </label>
                                <select name="start_time[]" id="start_time" class="form-select">
                                    <option value="" class="selected" disabled selected>Select</option>
                                   
                                </select>
                            </div>
        
                            <div class="col-12">
                                <label for="end_time" class="form-label mt-3 "> Finish hour </label>
                                <select name="end_time[]" id="end_time" class="form-select">
                                    <option value="" class="selected" disabled selected>Select</option>
                                    
                                </select>
                            </div>
        
                            <div class="col-12 mt-2 d-flex justify-content-end removeAddon">
                                <button type="button" class="btn btn-danger" id="removeAddon">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 d-none addAddon">
                        <div class="col md-6 d-flex justify-content-center">
                            <button type="button" class="btn btn-primary" id="addAddon">
                                Addon <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>

                </div>
                {{-- end container --}}
        </div>
        {{-- end modal body --}}
        
        <div class="modal-footer">
            <div class="col">
                <p>Available seat <span class="badge rounded-pill bg-danger d-none" id="spanAvailableSeat">
                </span>
                </p>
            </div>
            <button type="submit" class="btn btn-success">
                Next <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
      </div>
    </div>
</div>
{{-- akhir modal add --}}

<!-- modal detail booking -->
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
                                    <strong><p>Add-on</p></strong>
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
<!-- end modal detail booking -->

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
@endsection

@push('after-script')
    {{-- fullCalendar --}}
    <script src="{{ asset('assets/plugins/fullcalendar/main.min.js') }}"></script>

     {{-- select2 --}}
     <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
     
     {{-- sweetAlert --}}
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

     <script>
         @if(Session::has('data_booking'))
            $(window).on('load', function(){ 
                $('#modalAdd').modal('show');
                $('input#jumlah_seat').val('{{ Session::get('data_booking')['seat'] }}');
                $('#bookingDate').val('{{ Session::get('data_booking')['tanggal'] }}');
                $('h5.tanggalSelected').html( ( '{{ Session::get('data_booking')['tanggal'] }}' ) );
                if ({{ Session::get('data_booking')['shift']}} == '1') {
                    $('input#shift1').prop('checked', true);
                }else{
                    $('input#shift2').prop('checked', true);
                }
                if ({{ Session::get('data_booking')['seat'] }} > 1) {
                    $('#inviteRow').removeClass('d-none');
                }else{
                    $('#inviteRow').addClass('d-none');
                    $('select[name="invite[]"]').val('');
                }
                if ( {{ (Session::get('data_booking')['isAddon']) ? Session::pull('data_booking')['isAddon'] : '0' }} == 1) {
                    $('#isAddon').prop('checked', true);
                    $('#rowAddon').removeClass('d-none');
                    $('.addAddon').removeClass('d-none');
                }else{
                    $('#rowAddon').addClass('d-none');
                    $('.addAddon').addClass('d-none');
                    $('select[name="addon[]"]').val('');
                    $('#end_time').val('');
                    $('#start_time').val('');
                } 
            });
        @endif
     </script>

    
    
    {{-- custom js --}}
    <script>
        if ($(window).width() < 768) {
            Swal.fire({
                title: "Information",
                icon: "info",
                text: "Press and hold to choose date"
            });
        }

        if($('.addonForm').length == 1) {
            $('.removeAddon').addClass('d-none');
        }

        $('#start_time, #end_time').val('');
        // calendar
        moment.locale('en');
        var allSchedule = @json($allSchedule);
        var today = new Date().toISOString().slice(0,10);
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
        //   locale: 'id',
          height: 'auto',
          selectable: true,
          events: allSchedule,
          longPressDelay: 100,
          selectLongPressDelay: 100,
          eventLongPressDelay: 100,
          eventClick:  function(arg){
            $.ajax({
                type 	: 'POST',
                url		: '{{route("search_booking")}}',
                headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                dataType: 'JSON',
                data 	: {
                    'id'  :   arg.event.id,
                },
                success : function(rst) {
                    // assign variable header
                    const header = rst.header;
                    // cek data header
                    if (header) {
                        // assign variabel jika data header ada
                        const user_invited = rst.userInvited;
                        const addons = rst.addons;
                        const qrString = rst.qrCode;
                        const seat = user_invited.length;
                        if (header.end_time == '13:00') {
                           shiftLabel =  '08:00 - 13:00';
                        }else{
                            shiftLabel =  '13:00 - 18:00';
                        }
                        var dataUser = '';
                        var dataAddons = '';
                        var tanggal = moment(header.booking_date).format('dddd, Do MMMM YYYY');
                        // set detail header
                        $('h4.no_transaksi').text(header.transaksi_no);
                        $('p.nama_perusahaan').text(header.nama_perusahaan);
                        $('p.tanggal').text(tanggal);
                        $('p.seat').text(seat+' Seat, '+ shiftLabel);

                        // set user invited
                        if (user_invited.length > 0) {
                            dataUser += '<ol class="p-0 ms-3">';
                                $.each(user_invited,function(x,y){
                                    dataUser += '   <li>'+ y.nama_mitra+'</li>';
                                });
                            dataUser += '</ol>';
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
                            title: "Error !",
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
          select:   function(start){
            var dateNow = '{{ date("Y-m-d") }}';
            if (dateNow > start.startStr) {
                Swal.fire({
                    title: "Error",
                    icon: "warning",
                    text: "The date has passed!"
                });
                return;
            }
            $('input[name="shift"]').prop('checked', false);
            $('#spanAvailableSeat').addClass('d-none');
            $('input#jumlah_seat').val('');
            $('#bookingDate').val(start.startStr);
            $('h5.tanggalSelected').html(date_en(start.startStr));
            $('#inviteRow').addClass('d-none');
            $('select[name="invite[]"]').val('');
            $('#start_time').val('');
            $('#end_time').val('');
            $('#addon').val('');
            $('input#isAddon').prop('checked', false);
            $('#rowAddon').addClass('d-none');
            $('.addAddon').addClass('d-none');
            $('#modalAdd').modal('show');
          },
        //   end select object
        });
        // end full calendar init object

        // render full calendar
        calendar.render();

        $('[data-addon]:last #addon').change(function(){
            shift = $('input[type="radio"][name="shift"]:checked').val();
            addon = $(this).val();
            booking_date =  $('#bookingDate').val();
            $.ajax({
                type 	: 'POST',
                url		: '{{ route("getAddonTime") }}',
                headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                dataType: 'JSON',
                data 	: {
                    'shift' : shift,
                    'id_addon': addon,
                    'booking_date': booking_date,
                },
                success : function(rst) {
                    time = rst.time;
                    data = rst.data;
                    // console.log(data);return;
                    opt = '<option value="" disabled selected>Select</option>';
                    $.each(time,function(x,y){
                        opt += '<option value="'+y+'"';
                        if ( inArray(y,data) ) {
                        opt += 'disabled';
                        }
                        opt += '>'+y+'</option>';
                    });

                    $('[data-addon]:last #start_time').html(opt);
                    $('[data-addon]:last #end_time').html(opt);

                    var countDisabledOption = $('[data-addon]:last #start_time option:disabled').length;
                    if (countDisabledOption >= 6) {
                        notifMessage('Capacity full ', 'warning');
                        $('[data-addon]:last #start_time option').remove();
                        $('[data-addon]:last #end_time option').remove();
                        $('[data-addon]:last #addon').val('');
                    }
                },
                error 	: function(xhr) {
                    read_error(xhr);
                }
            });

        })

        $('input#jumlah_seat').change(function () {
            const seat =  $(this).val();
            if (seat > 1) {
                // alert('Please invite '+ (seat-1) + ' person' );
                $('#inviteRow').removeClass('d-none');
            }else{
                $('#inviteRow').addClass('d-none');
                $('select[name="invite[]"]').val('');
            }    
        });

        $('select#invite').change(function () {
            const invite =  $(this).val().length;
            $('input#jumlah_seat').val(invite+1);
            $('input#jumlah_seat').trigger('change');
        });
        
        $('input#isAddon').change(function () {
            if ($(this).is(":checked")) {
                $('#rowAddon').removeClass('d-none');
                $('.addAddon').removeClass('d-none');
            }else{
                $('#rowAddon').addClass('d-none');
                $('.addAddon').addClass('d-none');
                $('select[name="addon[]"]').val('');
                $('#end_time').val('');
                $('#start_time').val('');
            } 
        });

        $('#addAddon').click(function(){
            // $("#addonForm1:first").clone(true, true).appendTo("#rowAddon");
            let addonSelected = $('div[id^="addonForm"] select[id="addon"]').last().val();
            let startTimeSelected = $('div[id^="addonForm"] select[id="start_time"]').last().val();
            let endTimeSelected = $('div[id^="addonForm"] select[id="end_time"]').last().val();
            let arrTimeSelected = [
                '',
                startTimeSelected,
                endTimeSelected
            ];
            var $div = $('div[id^="addonForm"]:last');
            var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;

            var $clone = $div.clone(true, true).prop('id', 'addonForm'+num ).attr('data-addon', num);
            $clone.appendTo("#rowAddon");

            disableOption(addonSelected,startTimeSelected,arrTimeSelected);

            $('.removeAddon').removeClass('d-none');
        });

        function disableOption(addonSelected, startTimeSelected, arrTimeSelected){
            $('[data-addon]:last #addon option').each(function(){
                if (addonSelected == $(this).val()) {
                    $(this).prop('disabled', true);
                }
            });
            $('[data-addon]:last #start_time option').each(function(i){
                if (arrTimeSelected[i] == $(this).val()) {
                    $(this).prop('disabled', true);
                }
            });
            $('[data-addon]:last #end_time option').each(function(i){
                if (arrTimeSelected[i] == $(this).val()) {
                    $(this).prop('disabled', true);
                }
            });
        }

        $('#removeAddon').click(function(e){
            e.preventDefault();
            if($('.addonForm').length > 1) {
                $(this).closest(".addonForm").remove();
                if($('.addonForm').length == 1) {
                    $('.removeAddon').addClass('d-none');
                }
            }
        });

        $('[data-addon]:last select[id="end_time"]').change(function(){
            end_time = parseInt( $(this).val() );
            start_time = parseInt( $('[data-addon]:last #start_time').find(":selected").val() );

            if(start_time == end_time){
                notifMessage('Start and finish hours can not be same', 'warning');
                $('#end_time').val('');
                return;
            }else if(start_time > end_time){
                notifMessage('Start hour cannot be greater than the finish hour ', 'warning');
                $('#start_time').val('');
                $('#end_time').val('');
                return;
            }
        });

        $('[data-addon]:last select[id="start_time"]').change(function(){
            $('[data-addon]:last select[id="end_time"]').val('');
            start_time = $('[data-addon]:last #start_time').find(":selected").val();
            $('[data-addon]:last #end_time option').each(function(i){
                if (start_time == $(this).val()) {
                    $(this).prevUntil('.selected').addClass('d-none');
                    $(this).addClass('d-none');
                }else{
                    $(this).removeClass('d-none');
                }
            });
        });

        // shift on change
        $('#shift').change(function(){
            booking_date =  $('#bookingDate').val();
            shift = $('input[type="radio"][name="shift"]:checked').val();
            $('#addon').trigger('change');
            // ajax for check available quota
            $.ajax({
                type 	: 'POST',
                url		: '{{route("cek_data")}}',
                headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                dataType: 'JSON',
                data 	: {
                    'booking_date'  :   booking_date,
                    'shift': shift,
                },
                success : function(rst) {
                    sisa_seat = rst.sisa_seat;
                    $('input#jumlah_seat').attr('max', sisa_seat);
                    if (sisa_seat <= 0) {
                        Swal.fire({
                            title: "information",
                            icon: "info",
                            text: "The quota has run out !"
                        });
                        $('input[name="shift"]').prop('checked', false);
                    }else{
                        $('#spanAvailableSeat').removeClass('d-none');
                        $('#spanAvailableSeat').html(sisa_seat);
                    }
                },
                error 	: function(xhr) {
                    read_error(xhr);
                },
                complete : function(xhr,status){
                    Pace.stop();  
                }
            });
        // end ajax for check available quota
        });   
    // end onchange #shift 

    // on submit
    $('#formBooking').submit(function(e){
        e.preventDefault();
        var jumlah_seat = $('#jumlah_seat').val();
        var user_invited = $('#invite').val().length;
        var isAddon = $('input#isAddon').is(":checked");
        var addon = $('#addon').val();
        var shift = $('input[type="radio"][name="shift"]:checked').val();
        start_time = $('[data-addon]:last #start_time').find(":selected").val().length;
        end_time = $('[data-addon]:last #end_time').find(":selected").val().length;
        // console.log(start_time);
        // console.log( end_time);return;
        if (jumlah_seat <= 0 ) {
            notifMessage('Please input number of seat first ! ', 'warning');
            $('#jumlah_seat').val('');
            return;
        }
        
        if (shift == undefined ) {
            notifMessage('Please select shift first ! ', 'warning');
            return;
        }
        
        if (user_invited == 0 && jumlah_seat > 1){
            notifMessage('Please invite the person first ! ', 'warning');
            return;
        }

        if (isAddon === true && addon === null) {
            notifMessage('Please select add-on first ! ', 'warning');
            return;
        }

        if( isAddon === true ){
            if (start_time == 0 || end_time == 0) {
                notifMessage('Please select start and finish time first ! ', 'warning');
                return;
            }
        }

        this.submit();
    });
    // end on submit
    

    </script>
@endpush