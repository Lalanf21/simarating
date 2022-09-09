@extends('layout.master')
@section('title','Dashboard')
@push('after-style')
{{-- fullCalendar --}}
<link href="{{ asset('assets/plugins/fullcalendar-timeline/main.min.css') }}" rel="stylesheet" />
@endpush
{{-- custom style --}}
<style>
  #calendar-addon{
    cursor: pointer;
  }
  
  .fc-day-today {
        background-color: inherit !important;
   }

   .fc-event{
        cursor: pointer;
    }
</style>
{{-- end custom style  --}}

@section('content')
{{-- kalender --}}
<div class="row">
    <div class="col-md-6 p-3">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-dark">
            <i class="fas fa-list-alt"></i> Reservation
        </a>
        <a href="{{ route('time_table') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-door-open"></i> Add on room
        </a>
    </div>
</div>
<div class="card">
  <div class="card-body">
      <div class="container p-2">
          <div class="row">
              <div class="col-lg-12">
                  <div id="calendar-addon">

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
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body">
          <div class="card">
              <div class="card-body">
                  <div id="calendar-timeline" style="min-width: 500px">
                      
                  </div>
                
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{{-- akhir detail add --}}

@endsection


@push('after-script')
{{-- fullCalendar --}}
<script src="{{ asset('assets/plugins/fullcalendar-timeline/main.min.js') }}"></script>

{{-- chartJS --}}
<script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var bookingSchedule = @json($schedule);
    var calendarEl = document.getElementById('calendar-addon');

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
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        // locale: 'id',
        // eventLimit: true, 
        dayMaxEvents: true,
        height: 480,
        events: bookingSchedule,
        selectable: true,
        longPressDelay: 100,
        selectLongPressDelay: 100,
        eventLongPressDelay: 100,
        select: function(start){
            // show modal
            $('#modalDetail').modal('show');
            // set time out
            setTimeout(function() { 
                $.ajax({
                    type 	: 'POST',
                    url		: '{{route("getAddonTimeline")}}',
                    headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    dataType: 'JSON',
                    data 	: {
                        'tanggal' : start.startStr},
                    success : function(msg) {
                        var calendarTimeline = document.getElementById('calendar-timeline');
                        // console.log(msg.schedule_timeline);return;
                        var schedule = msg.schedule_timeline;
                        var addon = msg.addon;
                        var calendarTimelineEl = new FullCalendar.Calendar(calendarTimeline, {
                            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                            contentHeight: 'auto',
                            // resourceAreaWidth: '25%',
                            initialDate: start.startStr,
                            slotDuration: "01:00", //jarang antar jam = 1 jam
                            slotMaxTime: '19:00',
                            slotMinTime: '08:00',
                            slotLabelFormat: { 
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            },
                            headerToolbar: {
                                left:   '',
                                center: '',
                                right:  ''
                            },
                            eventTimeFormat: { 
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            },
                            // initialView: 'listWeek',
                            initialView: 'resourceTimelineDay',
                            resourceAreaHeaderContent: 'Add-on rooms',
                            resources: addon,
                            events: schedule,
                            eventClick: function(arg) {
                                Lobibox.alert('info',{
                                    title: 'Detail timeline',
                                    msg: arg.event.title+' : '+moment(arg.event.start).format('H:mm')+
                                    ' -'+moment(arg.event.end).format('H:mm') + ' WIB',
                                    modal  : true,
                                    closeOnEsc : true,
                                    closeButton     : false,
                                });  
                                console.log(arg.event);
                            },
                            // end event click
                        });
                    
                        calendarTimelineEl.render();
                    },
                    error 	: function(xhr) {
                        read_error(xhr);
                    }
                });
                // end ajax
            }, 250);
            // end Set time out
        }, 
        // end select click
        
  });
//   end init object calendar

//   render calendar
    calendar.render();
// akhir kalender
    
    // update size calendar
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
                        dataKuota += '<p>'+ y.nama_mitra+'</p>';
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
                            text: 'Number of reservation per date'
                        }
                    }
                }
            });

        }
    }
</script>

@endpush