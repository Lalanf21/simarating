@extends('layout.master')
@section('title','Data master - Laporan user co-working')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Laporan user co-working</h4>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12 data-box">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end mx-3 h-auto" >
                    <select name="id" id="id_asal_perusahaan" class="form-select form-select-sm me-3" style="max-width: 180px;">
                        <option value=" "> Asal perusahaan </option>
                        @foreach ($mitra as $value)
                        <option value="{{ $value->id }}">
                            {{ $value->nama }}
                        </option>
                        @endforeach
                    </select>

                    <div class="input-group input-group-sm search-box" style="max-width: 250px;">
                        <input name="" id="text-search" type="text" class="form-control" placeholder="">
                        <div class="input-group-append ms-2">
                            <span class="input-group-text" id="search-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                   
                    <a href="#" id="buttonExcel" type="button" class="btn ms-2 btn-sm btn-outline-primary" >
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="siswa" class="table align-items-center table-hover">
                        <thead style="cursor: pointer;">
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Asal perusahaan</th>
                                <th scope="col">No HP</th>
                                <th scope="col">Email</th>
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
                            <div class="mt-2 d-inline-block">
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

{{-- form export excel --}}
<form id="form_export_excel" action="{{ route('export_excel_user_co_working') }}" method="POST" style="display: block;">
    @method('post')
    @csrf
    <input type="hidden" name="hide_txt_search" id="hide_txt_search">
    <input type="hidden" name="hide_asal_perusahaan_search" id="hide_asal_perusahaan_search">
    <button class="d-none" id="btn_submit_export_excel">submit</button>
</form>
{{-- /form export excel --}}

@endsection

@push('after-script')
{{-- sweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    var max_row = {{$max_row}};
   var order_search = 0;
   var sort_search = 'asc';

   $(document).ready(function(){
       search_process();

       $("form").submit(function(e) {
           e.preventDefault();
       });
       
       $("#text-search, #id_asal_perusahaan").change(function(){
           search_process();
       });

       $('#buttonExcel').click(function(){
            var curentPage          = $('.data-box .card-footer .pagination li.active').text();
            var txtSearch           = $("#text-search").val();
            var asal_perusahaan     = $("#id_asal_perusahaan").val();

            $('#form_export_excel [name = "hide_txt_search"]').val(txtSearch);
            $('#form_export_excel [name = "hide_asal_perusahaan_search"]').val(asal_perusahaan);

            $('#form_export_excel').unbind('submit').submit();
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

       $.ajax({
           type 	: 'POST',
           url		: '{{route("search_user_co_working")}}',
           headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
           dataType: 'JSON',
           data 	: {
               'page'  :   search_page,
               'sort_search'   : sort_search,
               'order_search'  : order_search,
               'text_search' : text_search,
               'id_asal_perusahaan' : id_asal_perusahaan,
           },
           success : function(msg) {
               var rs = msg.rs_data;
               var dt = rs["data"];
               show_data(dt,1);
            //    console.log(dt);

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
           page = page+1;
           tbl += '<tr align="center">';
           tbl += '    <td>'+(page)+'</td>';
           tbl += '    <td>'+y.nama+'</td>';
           namaMitra = 'Tidak mitra'
           if (y.mitra !== null) {
               namaMitra = y.mitra.nama;
           }
           tbl += '    <td>'+namaMitra+'</td>';
           tbl += '    <td>'+y.no_hp+'</td>';
           tbl += '    <td>'+y.email+'</td>';
           tbl += '</tr>';

           });

           $('.data-box tbody').html(tbl);
       }else{
           tbl += '<tr align="center">';
           tbl += '    <td colspan="6">Data tidak ditemukan</td>';
           tbl += '</tr>';

           $('.data-box tbody').html(tbl);
       }
              
   }


   </script>
@endpush