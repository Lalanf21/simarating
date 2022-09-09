@extends('layout.master')
@section('title','Add schedule')
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

    .fc-day{
        cursor: pointer;
    }
</style>

<div class="card">
    <div class="card-header">
      <h3>Choose reservation date</h3>
    </div>
</div>

{{-- kalender --}}
<div class="card">
    <div class="card-body">
        <div class="container p-2">
            <div class="row">
                <div class="col-lg-12">
                    <div id="calendar" style="width: 100%;">

                    </div>
                    <input type="hidden" name="date" id="date">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- /kalender --}}

{{-- modal --}}
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-center tanggalSelected">

            </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
           
            <form action="{{ route('confirmation-schedule') }}" method="post" id="formBooking">
                @csrf
                @method('POST')
                <div class="container">
                    <input type="hidden" name="tanggal_booking" class="form-control" id="bookingDate">

                    <div class="col-12">
                        <label for="id_mitra_user" class="form-label">Name</label>
                        <input type="hidden" name="id_mitra">
                        <select name="id_mitra_user" id="id_mitra_user"  class="form-select nama_user @error('id_mitra_user') is-invalid @enderror" id="id_mitra_user">
                            <option value="">Select</option>
                            @foreach ($mitra as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->nama.' - '. $value->mitra->nama_perusahaan }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_mitra_user')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    
                    <div class="col-12 mt-2">
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
                                <label class="form-check-label" for="shift1"> 
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
                                <select name="invite[]" id="invite" class="form-select invitePerson @error('invite') is-invalid @enderror" multiple="multiple">
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
                                <label for="start_time" class="form-label mt-3 "> Starting hour </label>
                                <select name="start_time[]" id="start_time" class="form-select">
                                    <option value="" class="selected" disabled selected >Select</option>
                                    
                                </select>
                            </div>
        
                            <div class="col-12">
                                <label for="end_time" class="form-label mt-3 "> Finish hour </label>
                                <select name="end_time[]" id="end_time" class="form-select">
                                    <option value="" class="selected" disabled selected >Select</option>
                                    
                                </select>
                            </div>
        
                            <div class="col-12 mt-2 d-flex justify-content-end removeAddon">
                                <button type="button" class="btn btn-danger" id="removeAddon">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                            <hr class="mt-3 text-muted">
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
{{-- akhir modal --}}
@endsection

@push('after-script')
{{-- fullCalendar --}}
<script src="{{ asset('assets/plugins/fullcalendar/main.min.js') }}"></script>

{{-- select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

 {{-- sweetAlert --}}
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

{{-- custom js --}}
<script>
    @if(Session::has('data_booking'))
    $(window).on('load', function(){ 
        $('#modal').modal('show');
        $('input#jumlah_seat').val('{{ Session::get('data_booking')['seat'] }}');
        $('select#id_mitra_user').val('{{ Session::get('data_booking')['id_mitra_user'] }}');
        $('select#id_mitra_user').trigger('change');
        $('#bookingDate').val('{{ Session::get('data_booking')['tanggal'] }}');
        $('h5.tanggalSelected').html( ( '{{ date_en_full(Session::get('data_booking')['tanggal']) }}' ) );
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
<script>
    

    $( document ).ready(function() {
        if($('.addonForm').length == 1) {
            $('.removeAddon').addClass('d-none');
        }

        if ($(window).width() < 768) {
            Swal.fire({
                title: "Information",
                icon: "info",
                text: "Press and hold to choose date"
            });
        }
        
        $('#wrapper').addClass('toggled');
        $(".sidebar-wrapper").hover(function() {
                $(".wrapper").addClass("sidebar-hovered")
            }, function() {
                $(".wrapper").removeClass("sidebar-hovered")
        })
        $('#start_time, #end_time').val('');

        // calendar
        var today = new Date().toISOString().slice(0,10);
        var calendarEl = document.getElementById('calendar');
        var reservasiSchedule = @json($reservasi);
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            // locale: 'id',
            dayMaxEventRows: 2,
            // height: 450,
            contentHeight:"auto",
            selectable: true,
            events: reservasiSchedule,
            longPressDelay: 100,
            selectLongPressDelay: 100,
            eventLongPressDelay: 100,
            select:   function(start){
                var shift = $('#hiddenShift').val();
                var dateNow = '{{ date("Y-m-d") }}';
                if (dateNow > start.startStr) {
                    Swal.fire({
                        title: "Error",
                        icon: "warning",
                        text: "The date has passed !"
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
                $('#modal').modal('show');
            },
        //   end select object
        });
        // end full calendar init object

        // render full calendar
        calendar.render();

        //  Update size full calendar
        setTimeout(function() { 
            calendar.updateSize();
        }, 250);
    });
    // end on ready function
    
    

    $('.invitePerson').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modal'),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder:'Select',
        allowClear: Boolean($(this).data('allow-clear')),
        multiple: true,
    });

    $('.nama_user').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modal'),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: 'Select',
    });

    // set autofocus search select2 
    $(document).on('select2:open', () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });


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
    
    $('input#isAddon').change(function () {
        if ($(this).is(":checked")) {
            $('#rowAddon').removeClass('d-none');
            $('.addAddon').removeClass('d-none');
        }else{
            $('#rowAddon').addClass('d-none');
            $('.addAddon').addClass('d-none');
            $('select[name="addon[]"]').val('');
            $('select[name="start_time[]"]').val('');
            $('select[name="end_time[]"]').val('');
        } 
    });

    $('select#invite').change(function () {
            const invite =  $(this).val().length;
            $('input#jumlah_seat').val(invite+1);
            $('input#jumlah_seat').trigger('change');
        });

    $('#id_mitra_user').change(function() {
        var id_mitra_user = $(this).val();
        $.ajax({
        type 	: 'POST',
        url		: '{{ route("get_perusahaan_code") }}',
        headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        dataType: 'JSON',
        data 	: 
        {
            'id': id_mitra_user,
            },
            success: function(data) {
                $('input[name=id_mitra]').val(data.mitra.id);
                get_list_mitra(data.id, data.mitra.id);
            }
        });
    });

    function get_list_mitra(id_mitra_user, id_mitra){
        $.ajax({
            type 	: 'POST',
            url		: '{{ route("get_list_mitra") }}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: {
                'id_mitra' : id_mitra,
                'id_mitra_user' : id_mitra_user,
            },
            success : function(msg) {
                opt = '';
                $.each(msg,function(x,y){
                    opt += '<option value="'+y.id+'">'+y.nama+' - '+y.mitra.nama_perusahaan+'</option>'
                });
                $('#invite').html(opt);
            },
            error 	: function(xhr) {
                read_error(xhr);
            }
        });
    }

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
                    // console.log(xhr);
                }
            });

        })

    $('[data-addon]:last select[id="end_time"]').change(function(){
        end_time = parseInt($(this).val());
        start_time = parseInt($('#start_time').find(":selected").val());

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


    $('#shift').change(function(){
            booking_date =  $('#bookingDate').val();
            shift = $('input[type="radio"][name="shift"]:checked').val();
            $('#addon').val('');
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

    $('#addAddon').click(function(){
        // var cloned = $("#addonForm1:first").clone(true, true);
        // cloned.appendTo("#rowAddon");
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
            if (arrTimeSelected[i] === $(this).val()) {
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

    // on submit
    $('#formBooking').submit(function(e){
        e.preventDefault();
        var jumlah_seat = $('#jumlah_seat').val();
        var user_invited = $('#invite').val().length;
        var isAddon = $('input#isAddon').is(":checked");
        var addon = $('#addon').val();
        var shift = $('input[type="radio"][name="shift"]:checked').val();
        var id_mitra_user = $('select#id_mitra_user').val().length;
        var start_time = $('[data-addon]:last #start_time').find(":selected").val().length;
        var end_time = $('[data-addon]:last #end_time').find(":selected").val().length;
        // console.log(start_time);
        // console.log( id_mitra_user);return;
        if (id_mitra_user <= 0 ) {
            notifMessage('Please select name first ! ', 'warning');
            $('#id_mitra_user').val('');
            return;
        }
        
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