@extends('layout.master')
@section('title','Reservation data')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Reservation data</h4>
        <hr>
    </div>
</div>

<div class="d-md-flex flex-row-reverse mb-2">
    <a href="{{ route('add_schedule') }}" type="button" class="btn m-2 ms-2 btn-sm btn-outline-primary">
        <i class="fas fa-plus"></i> Add new
    </a>

    <div class="input-group input-group-sm search-box m-2 " style="max-width: 250px;">
        <input name="" id="text-search" type="text" class="form-control" placeholder="">
        <div class="input-group-append ms-2">
            <span class="input-group-text" id="search-addon"><i class="fa fa-search"></i></span>
        </div>
    </div>   

    <select name="id" id="id_asal_perusahaan" class="form-select form-select-sm m-2" style="max-width: 150px;">
        <option value=" "> Company </option>
        @foreach ($mitra as $value)
        <option value="{{ $value->id }}">
            {{ $value->nama_perusahaan }}
        </option>
        @endforeach
    </select>

    <input type="date" name="tanggal" id="tanggal" class="form-control m-2 form-control-sm me-3" style="max-width: 180px;">
</div>

<div class="row">
    <div class="col-md-12 data-box">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="siswa" class="table align-items-center table-hover">
                        <thead style="cursor: pointer;">
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Transaction number</th>
                                <th scope="col">Company</th>
                                <th scope="col">Reservation date</th>
                                <th scope="col">Option</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
    
                    </table>
                    
                </div>
            </div>

            {{-- pagination --}}
            <div class="card-footer">
                <div class="row">
                        <div class="col-md-4">
                            <div style="margin-top: 16px; display: inline-block; font-size: 12px; color:#777">
                                
                                <span class="from-data">0</span>-<span class="to-data">0</span>
                                of 
                                <span class="total-data">0</span>
                            </div>
                            
                        </div>
                        <div class="col-md-8 d-flex pagination-box justify-content-end">

                        </div>
                </div>        
            </div>
        </div>
    </div>
</div>

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
                    <h4 class="text-center titleModal"> </h4>
                </div>
                <div class="card-body">
                    <div class="container">
                        <div class="row p-3">
                            <div class="col-md-6">
                                <strong><p>Transaction number</p></strong>
                                <p class="no_transaksi"></p>
                            </div>
                            <div class="col-md-6">
                                <strong><p>Company number</p></strong>
                                <p class="nama_perusahaan"> </p>
                            </div>
                        </div>

                        <div class="row p-3">
                            <div class="col-md-6">
                                <strong><p>Reservation date</p></strong>
                                <p class="tanggal"></p>
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
                                <strong><p>Shift</p></strong>
                                <p class="shift">
                                    
                                </p>
                            </div>
                        </div>

                        <div class="row p-3">
                            <div class="col-md-12">
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
{{-- akhir detail add --}}
@endsection

@push('after-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    // moment.locale('id');
    var max_row = {{$max_row}};
    var order_search = 0;
    var sort_search = 'asc';
 
    $(document).ready(function(){
        search_process();
 
        // $("form").submit(function(e) {
        //     e.preventDefault();
        // });
        
        $("#text-search, #id_asal_perusahaan, #tanggal").change(function(){
            search_process();
        });
 
        $(document).on( "click", ".data-box .card-footer .pagination li", function(e) {
            e.preventDefault();
            var curentPage = $('.data-box .card-footer .pagination li.active').text();
            var page = $.trim($(this).text());
            var liIndex = $(this).index();
            var totalPage = $("#total_page").val();
            
            if(isNaN(page))
            {
                if(page === '›'){
                    page = parseInt(curentPage) + 1;
 
                    if(page > totalPage){
                        page = parseInt(totalPage);
                    }
                    
                }
 
                if(page === '‹'){
                    page = parseInt(curentPage) - 1;
 
                    if(page === 0){
                        page = 1;
                    }
                }
 
                $('.data-box .card-footer .pagination li').each(function(){
                    var liPage = $(this).text();
                    var liPageIndex = $(this).index();
                    if(page == liPage){
                        $('.data-box .card-footer .pagination li').removeClass('active');
                        $('.data-box .card-footer .pagination li').eq(liPageIndex).addClass('active');
                    }
                }) ;   
            }else{
                
                $('.data-box .card-footer .pagination li').removeClass('active');
                $('.data-box .card-footer .pagination li').eq(liIndex).addClass('active');  
            }
            
            
            var textSearch = $("#old_search_text").val();
 
            pagination_process(parseInt(page),textSearch);
        });
 
        $('.data-box table thead th').click(function(){
            var columnIndex = $(this).index();
 
                //Jika selain column action
                if(columnIndex != 0){
                    order_search = columnIndex;
                    if(sort_search == 'asc'){
                        sort_search = 'desc';
                    }else{
                        sort_search = 'asc';
                    }
                    
                    search_process();
                }
            
            });
    });
 
    var search_page = 1;
 
    function search_process(){
         Pace.start();
         text_search = $.trim($("#text-search").val());
         id_asal_perusahaan = $.trim($("#id_asal_perusahaan").val());
         tanggal = $.trim($("#tanggal").val());
 
        $.ajax({
            type 	: 'POST',
            url		: '{{route("search_schedule")}}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: {
                'page'  :   search_page,
                'sort_search'   : sort_search,
                'order_search'  : order_search,
                'text_search' : text_search,
                'id_asal_perusahaan' : id_asal_perusahaan,
                'tanggal' : tanggal,
            },
            success : function(msg) {
                var rs = msg.rs_data;
                var dt = rs["data"];
                show_data(dt,1);
 
                 // Untuk pagination dan total data  to-data total-data
                $('.data-box .card-footer .from-data').html(rs.from);
                $('.data-box .card-footer .to-data').html(rs.to);
                $('.data-box .card-footer .total-data').html(rs.total);   
                $('.data-box .card-footer .pagination-box').html($(msg.pagination));
 
            },
            error 	: function(xhr) {
                read_error(xhr);
            },
            complete : function(xhr,status){
                 Pace.stop();  
            }
        });
        
    }
    
    function pagination_process(page,textSearch){
        search_page = page;
        search_process();
    }
 
    function show_data(dt,page){
        var tbl = '';
        page = (page * max_row) - max_row;
        if(dt.length > 0){
            $.each(dt,function(x,y){
            var tanggal = moment(y.booking_date).format('dddd, Do MMMM YYYY');
            page = page+1;
            namaAddon = 'No add-on';
            tbl += '<tr align="center">';
            tbl += '    <td>'+(page)+'</td>';
            tbl += '    <td>'+y.transaksi_no+'</td>';
            tbl += '    <td>'+y.nama_perusahaan+'</td>';
            tbl += '    <td>'+tanggal+'</td>';
            tbl += '    <td align="right">';
            tbl += '        <div class="btn-group" role="group" aria-label="Button Control">';
            tbl += '             <button type="button" class="btn-detail btn btn-sm btn-light" onclick="detail_data(\''+y.id+'\')"><i class="fas fa-eye"></i></button>';
            tbl += '             <button type="button" class="btn-delete btn btn-sm btn-light text-danger" onclick="delete_process(\''+y.id+'\')"><i class="fas fa-trash-alt"></i></button>';
            tbl += '        </div>';
            tbl += '    </td>';
            tbl += '</tr>';
 
            });
 
            $('.data-box tbody').html(tbl);
        }else{
            tbl += '<tr align="center">';
            tbl += '    <td colspan="5">Data not found</td>';
            tbl += '</tr>';
 
            $('.data-box tbody').html(tbl);
        }
               
    }
  
    function detail_data(id){
        $.ajax({
            type 	: 'POST',
            url		: '{{route("detail_data_schedule")}}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: {
                'id' : id},
            success : function(msg) {
                userInvited = msg.userInvited;
                addons = msg.addons;
                seat = userInvited.length;
                header = msg.header;
                var dataUser = '';
                var dataAddons = '';
                var tanggal = moment(header.booking_date).format('dddd, Do MMMM YYYY');
                $('h4.titleModal').text('Detail data');
                $('p.no_transaksi').text(header.transaksi_no);
                $('p.nama_perusahaan').text(header.nama_perusahaan);
                // shift
                if (header.end_time == '13:00') {
                    $('p.shift').text('Morning 08:00 - 13:00');
                }else{
                    $('p.shift').text('Afternoon 13:00 - 18:00');
                }
                $('p.tanggal').text(tanggal);
                $('p.seat').text(seat+' Seat');
                
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
        
    }

    function delete_process(id){
        Swal.fire({
            title: "Are you sure?",
            icon: "question",
            showDenyButton: true,
            denyButtonColor:'#3085d6',
            denyButtonText: `Cancel`,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#d33',
        })
        .then((result) => {
            if (result.isConfirmed) {
                var dataForm 		= $('#form').serializeArray();
                $.ajax({
                    type 	: 'POST',
                    url		: '{{ route("delete_schedule") }}',
                    headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    dataType: 'JSON',
                    data 	: {
                        'id': id,

                    },
                    success : function(msg) {
                        pagination_process(1,'');
                        notifMessage('Success delete data !', 'error');
                        $('#modal').modal('hide');
                    },
                    error 	: function(xhr) {
                        read_error(xhr);
                    }
                });
            } 
        });  
   }
 
 
    </script>
@endpush