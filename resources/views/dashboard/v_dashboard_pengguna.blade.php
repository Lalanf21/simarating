@extends('layout.master')
@section('title','Dashboard')

@push('after-style')
{{-- owlCarousel --}}
<link href="{{ asset('assets/plugins/OwlCarousel/css/owl.carousel.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/OwlCarousel/css/owl.theme.default.min.css') }}" rel="stylesheet" />

{{-- perfect scrollbar --}}
<link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="card">
    <div class="card-header">
      <h5>Upcoming reservation</h5>
    </div>

  <div class="col d-flex justify-content-end">
    <a href="{{ route('riwayat-booking') }}" class="me-3 badge text-black">
      <p>More</p>
    </a>
  </div>
  <div class="card-footer">
      <div class="owl-carousel owl-theme">
        @forelse ($bookings as $booking)
        <div class="card radius-10 w-auto">
          <div class="row g-0 align-items-center">
            <div class="col-md-4">
              <div class="p-3">
                <a href="" data-bs-toggle="modal" data-bs-target="#modalQrCode{{ $booking->transaksi_no }}">
                  <img src="{{ asset('/storage/img/qr-codes/'.$booking->qr_code_string.'/qrcode.png') }}" class="img-fluid radius-10" alt="Qr code">
                </a>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <p class="card-title h-6">
                  <strong>{{ date_en_full($booking->booking_date) }} </strong>
                </p>
                <p class="card-text"><small class="text-muted">{{ $booking->qr_code_string }}</small></p>
                <a href="javascript:;" onclick="seeDetail({{ $booking->id }})">
                  See detail
                </a>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="card radius-10 w-auto mt-3">
          <div class="row g-0 align-items-center">
            <div class="col-12">
              <div class="card-body">
                <p class="card-title text-center">
                  <strong>No upcoming reservation </strong>
                </p>
              </div>
            </div>
          </div>
        </div>
        @endforelse
      </div>
  </div>
</div>

<div class="row text-center">
  <div class="col-md-4">
    <div class="card radius-10 ">
      <div class="card-header text-white" style="background-color: #d13adf">
        <h5>Used credit</h5>
      </div>
      <div class="card-body justify-content-center">
        <div class="chart easy-pie-chart-kuota" data-percent="{{ $terpakai }}">
          <span class="percent"></span>
        </div>
        <hr>
        <p class="mb-0">
          <strong>
            {{ ($kuota) ? $terpakai.' from '.$kuota : '0 from 0' }}
          </strong>
        </p>
      </div>
    </div>
  </div>
</div>

 <!-- modalQrCode -->
 @foreach ($bookings as $qrcode)
<div id="modalQrCode{{ $qrcode->transaksi_no }}" class="modal fade" tabindex="-1" role="dialog"
  aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('/storage/img/qr-codes/'.$qrcode->qr_code_string.'/qrcode.png') }}" width="100%">
        <h5 class="text-center" id="qrCodeString"> {{ $qrcode->qr_code_string }} </h5>
      </div>
    </div>
  </div>
</div>
@endforeach
<!-- end modalQrCode -->

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
@endsection

@push('after-script')
{{-- owlCarousel --}}
<script src="{{ asset('assets/plugins/OwlCarousel/js/owl.carousel.min.js') }}"></script>

{{-- sweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

{{-- progress bar --}}
<script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>

{{-- custom js --}}
<script>
  @if(Session::has('booking'))
  Pace.stop();
    Swal.fire({
      title: "{{ Session::get('booking') }}",
      icon: "error",
      text: "{{ Session::get('booking') }}"
    });
  @endif

  @if(Session::has('alert_message'))
      Swal.fire({
          icon: "{{ Session::get('alert_type') }}",
          text: "{{ Session::get('alert_message') }}"
      });
  @endif
  
  $(document).ready(function(){
    $(".owl-carousel").owlCarousel({
      margin:0,
      // loop:true,
      margin:10,
      responsiveClass:true,
      touchDrag: true,
      // autoWidth:true,
      autoHeight: true,
      // width: 700,
      responsive:{
          300:{
              items:1,
          },
          600:{
              items:3,
          },
      }
    });

    $('.easy-pie-chart-kuota').easyPieChart({
			easing: 'easeOutBounce',
			barColor : '#d13adf',
			lineWidth: 6,
			animate: 1000,
            lineCap: 'rgba(209, 58, 223, 0.25)',
            trackColor : 'rgba(209, 58, 223, 0.25)',
			onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}
		});
    
  });
</script>
<script>
  function seeDetail(id){
    $.ajax({
      type 	: 'POST',
      url		: '{{route("search_booking")}}',
      headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
      dataType: 'JSON',
      data 	: {
          'id'  :  id,
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
  }
</script>
@endpush