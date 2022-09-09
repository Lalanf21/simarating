@extends('layout.master')
@section('title','Dashboard')

@push('after-style')
{{-- fullCalendar --}}
<link href="{{ asset('assets/plugins/fullcalendar/main.min.css') }}" rel="stylesheet" />

{{--  --}}
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
{{-- kalender --}}
<div class="row">
  <div class="col-md-6 p-3">
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-list-alt"></i> Reservation
    </a>
    <a href="{{ route('time_table') }}" class="btn btn-sm btn-dark">
        <i class="fas fa-door-open"></i> Add on room
    </a>
  </div>
</div>
<div class="card">
  <div class="card-hbody">
      <div class="container p-2">
          <div class="row">
              <div class="col-lg-12">
                  <div id="calendar">

                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
{{-- /kalender --}}

{{-- chart --}}
<div class="card p-3">
    <div class="row d-flex justify-content-center my-2">
        <div class="col-4 ">
            <select id="tahun_filter" class="form-select">
                <option value="">Select year</option>
                @for($i=2020;$i<=2025;$i++)
                    <option value="{{$i}}">{{$i}}</option>
                @endfor
            </select>
        </div>
        <div class="col-4 ">
            @php 
                $month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            @endphp
            <select id="bulan_filter" class="form-select">
                <option value="">Select month</option>
                @for($i=0;$i<=11;$i++)
                    <option value="{{$i+1}}">{{$month[$i]}}</option>
                @endfor
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
              <h6 class="mb-0"> Used credit </h6>
            </div>
            <div class="card-body">
                <div id="chartKuota">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-graph">
                <canvas id='show-graph'> </canvas>
            </div>
        </div>
    </div>
</div>
{{-- end chart --}}

{{-- modal detail data --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-body">
          <div class="card">
              <div class="card-footer bg-dark text-white">
                  <h4 class="text-center no_transaksi"> </h4>
              </div>
              <div class="card-body">
                  <div class="container">
                      <div class="row">
                          <div class="col-12">
                              <img src="" id="qrCode">
                          </div>
                      </div>
                      <div class="row p-3">
                            <div class="col-md-6">
                                <strong><p>Company name</p></strong>
                                <p class="nama_perusahaan"> </p>
                            </div>
                            <div class="col-md-6">
                            <strong><p>Reservation date</p></strong>
                            <p class="tanggal"></p>
                            </div>
                      </div>

                      <div class="row p-3">
                          <div class="col-md-6">
                              <strong><p>Shift</p></strong>
                              <p class="shift"></p>
                          </div>
                          <div class="col-md-6">
                              <strong><p>Number of seat</p></strong>
                              <p class="seat"> </p>
                          </div>
                      </div>
           
                      <div class="row p-3">
                          <div class="col-md-6">
                              <strong><p>Name</p></strong>
                              <div id="user_invited">
                                  
                              </div>
                          </div>
                          <div class="col-md-6">
                              <strong><p>Add-on</p></strong>
                              <div id="addons">
                                  
                              </div>
                          </div>
                        </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>
{{-- akhir detail data --}}

@endsection

@push('after-script')
{{-- fullCalendar --}}
<script src="{{ asset('assets/plugins/fullcalendar/main.min.js') }}"></script>

{{-- chartJS --}}
<script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {
    // moment.locale('id');
    var reservasiSchedule = @json($schedule);
    var calendarEl = document.getElementById('calendar');

    if ($(window).width() > 768) {
        $('#wrapper').addClass('toggled');
        $(".sidebar-wrapper").hover(function() {
                $(".wrapper").addClass("sidebar-hovered")
            }, function() {
                $(".wrapper").removeClass("sidebar-hovered")
        })
    }

//   init calendar object
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        // locale: 'id',
        height: 480,
        stickyHeaderDates: true,
        dayMaxEvents: true,
        events: reservasiSchedule,
        longPressDelay: 100,
        selectLongPressDelay: 100,
        eventLongPressDelay: 100,
        eventClick: function(arg) {
            $.ajax({
                type 	: 'POST',
                url		: '{{route("detail_data_schedule")}}',
                headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                dataType: 'JSON',
                data 	: {
                    'id' : arg.event.id
                },
                success : function(msg) {
                    userInvited = msg.userInvited;
                    addons = msg.addons;
                    seat = userInvited.length;
                    header = msg.header;
                    var dataUser = '';
                    var dataAddons = '';
                    var tanggal = moment(header.booking_date).format('dddd, Do MMMM YYYY');

                    $('h4.titleModal').text('Detail data');
                    $('h4.no_transaksi').text(header.transaksi_no);
                    $('p.nama_perusahaan').text(header.nama_perusahaan);
                    $('p.tanggal').text(tanggal);
                    $('p.seat').text(seat+' Seat');
                    
                    // shift
                    if (header.end_time == '13:00') {
                        $('p.shift').text('Morning 08:00 - 13:00');
                    }else{
                        $('p.shift').text('Afternoon 13:00 - 18:00');
                    }

                    // user invited
                    if (userInvited.length > 0) {
                        dataUser += '<ol class="p-0 ms-3">';
                            $.each(userInvited,function(x,y){
                                dataUser += '   <li>'+ y.nama_mitra+'</li>';
                            });
                        dataUser += '</ol>';
                        $('#user_invited').html(dataUser);
                    }

                    if (addons.length > 0) {
                        $.each(addons,function(x,y){
                            dataAddons += '<p>'+ y.nama_addon+ ' : ' + y.start_time+ ' - ' + y.end_time +'</p>';
                        });
                    }else{
                        dataAddons += '<p> No add-on </p>';
                    }
                    $('#addons').html(dataAddons);
                    
                    $('#modalDetail').modal('show');
                },
                error 	: function(xhr) {
                    read_error(xhr);
                }
            });
                // end ajax
        },
        // end event click
  });
//   end init object calendar

//   render calendar
    calendar.render();
// akhir kalender

    // Update size calendar
    setTimeout(function() { 
        calendar.updateSize();
    }, 250);
    // akhir update size calendar

  
});
// akhir on ready

</script>

<script>
    var myChart;
    $(document).ready(function(){
        var year = $("#tahun_filter").val('{{date("Y")}}');
        var month = $("#bulan_filter").val('{{ (int) date("m") }}');
        
        search_process();
        search_kuota();
        
        $("#tahun_filter, #bulan_filter").change(function(){
            search_process();
            search_kuota();
        });

    });

    function search_kuota(){
        var year = $("#tahun_filter").val();
        var month = $("#bulan_filter").val();
        var data = {
            'year':year,
            'month':month
        };
        $.ajax({
            type 	: 'POST',
            url		: '{{route("getChartKuotaTerpakai")}}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: data,
            success : function(msg) {
                // show_data(msg);
                var rsData = msg.data
                var dataKuota = '';
                // console.log(rsData);return;
                if (rsData.length > 0) {
                    $.each(rsData,function(x,y){
                        dataKuota += '<p style="margin-bottom: -2.5px;">'+ y.nama_mitra+'</p>';
                        dataKuota += '<div class="progress mb-3" style="height:25px;">';
                            dataKuota += '<div class="progress-bar bg-info" role="progressbar" aria-valuenow="'+y.total_seat+'" aria-valuemin="0" aria-valuemax="'+y.kuota+'" style="width:'+y.total_seat+'%">';
                            dataKuota += '</div>';
                        dataKuota += '</div>';
                        dataKuota += '<p class="text-muted" style="margin-top:-15px;">'+y.total_seat+' from '+y.kuota+'</p>';
                    });
                }else{
                    dataKuota += '<p></p>';
                        dataKuota += '<div class="progress mb-3" style="height:25px;">';
                            dataKuota += '<div class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" style="width:0%">';
                            dataKuota += '<strong style="font-color: black !important"> 0 from 0</strong>';
                            dataKuota += '</div>';
                        dataKuota += '</div>';
                }
                $('#chartKuota').html(dataKuota);
            },
            error 	: function(xhr) {
                read_error(xhr);
            }
        })
    }

    function search_process(){
        var year = $("#tahun_filter").val();
        var month = $("#bulan_filter").val();
        var data = {
            'year':year,
            'month':month
        };
        $.ajax({
            type 	: 'POST',
            url		: '{{route("getChartBooking")}}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: data,
            success : function(msg) {
                show_data(msg);
            },
            error 	: function(xhr) {
                read_error(xhr);
            }
        })
    }

    function show_data(data){
        if(data.type == "year"){
            if(myChart != null || myChart != undefined){
                myChart.destroy();
            }
            var data_f = data.data;
            var ctx = document.getElementById('show-graph').getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data_f.labels,
                    datasets: [{
                        label: 'Reservation',
                        data: data_f.datasets,
                        backgroundColor: "#32bfff",
                        borderColor: "#32bfff",
                        borderWidth: 2,
                        animations: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    title: {
                            display: true,
                            text: 'Number of reservation per year'
                        }
                    }
                }
            });
        }else if(data.type == "month"){
            if(myChart != null || myChart != undefined){
                myChart.destroy();
            }
            var data_f = data.data;
            var ctx = document.getElementById('show-graph').getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data_f.labels,
                    datasets: [{
                        label: 'Resevation',
                        data: data_f.datasets,
                        backgroundColor: "#32bfff",
                        borderColor: "#32bfff",
                        borderWidth: 2,
                        animations: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    title: {
                            display: true,
                            text: 'Number of reservation per month'
                        }
                    }
                }
            });

        }else if(data.type == "date"){
            if(myChart != null || myChart != undefined){
                myChart.destroy();
            }
            var data_f = data.data;
            var ctx = document.getElementById('show-graph').getContext('2d');
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data_f.labels,
                    datasets: [{
                        label: 'Resevation',
                        data: data_f.datasets,
                        backgroundColor: "#32bfff",
                        borderColor: "#32bfff",
                        borderWidth: 2,
                        animations: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    title: {
                            display: true,
                            text: 'Number of reservation per date'
                        }
                    }
                }
            });

        }
    }
</script>
@endpush