@extends('layout.master')
@section('title','Add on room')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Add-on room</h4>
        <hr>
    </div>
</div>
<div class="d-md-flex flex-row-reverse mb-2">
    <a href="" id="buttonAdd" type="button" class="btn ms-2 btn-sm btn-outline-info" data-bs-toggle="modal" >
        <i class="fas fa-plus"></i> Add new
    </a>
    <div class="input-group input-group-sm float-right search-box" style="max-width: 250px;">
        <input name="" id="text-search" type="text" class="form-control" placeholder="search...">
        <div class="input-group-append ms-2">
            <span class="input-group-text" id="search-addon"><i class="fa fa-search"></i></span>
        </div>
    </div>
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
                                <th scope="col">Name</th>
                                <th scope="col">Status</th>
                                <th scope="col" width="150">Option</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
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

{{-- modal add --}}
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action=""  method="POST" id="form" >
                @csrf
                @method('POST')
                <input type="hidden" name="id">
                <label for="nama">Name</label>
                <input type="text" name="nama" id="nama" class="form-control mb-3" value="{{ old('nama') }}" required>
               
                <label for="status">Status </label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- Select --</option>
                    <option value="1">Active</option>
                    <option value="0">Non-active</option>
                </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button id="btnSave" type="submit" class="btn btn-outline-success">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </form>
      </div>
    </div>
  </div>
{{-- akhir modal add --}}
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
       $('#buttonAdd').click(function(){
           $('#modal .modal-title').text('Insert data');
           $('#modal #form').attr('action','{{ route("add_addon_room") }}');
           $('#modal select').prop("disabled", false);
           $('#modal input').prop("readonly", false);
           $('#form  input[name="nama"]').val('');
           $('#form  select[name="status"]').val('');
           $('#modal').modal('show');
       });

       
       $("#text-search").change(function(){
           var textSearch = $(this).val();
           search_process(textSearch);
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

   function search_process(text_search){
        Pace.start();
        text_search = $.trim(text_search);

       $.ajax({
           type 	: 'POST',
           url		: '{{route("search_addon_room")}}',
           headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
           dataType: 'JSON',
           data 	: {
               'page'  :   search_page,
               'sort_search'   : sort_search,
               'order_search'  : order_search,
               'text_search' : text_search
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
           page = page+1;

           tbl += '<tr align="center">';
           tbl += '    <td>'+(page)+'</td>';
           tbl += '    <td>'+y.nama+'</td>';
           if (y.status == '1') {
               tbl += '    <td> <i class="fas fa-check"></i> </td>';
            }else{
               tbl += '    <td> <i class="fas fa-times"></i> </td>';
           }
           tbl += '    <td align="right">';
           tbl += '        <div class="btn-group" role="group" aria-label="Button Control">';
           tbl += '             <button type="button" class="btn-edit btn btn-sm btn-light text-info" onclick="edit_data(\''+y.id+'\')"><i class="fas fa-pencil-alt"></i></button>';
           tbl += '             <button type="button" class="btn-delete btn btn-sm btn-light text-danger" onclick="delete_process(\''+y.id+'\')"><i class="fas fa-trash-alt"></i></button>';
           tbl += '        </div>';
           tbl += '    </td>';
           tbl += '</tr>';

           });

           $('.data-box tbody').html(tbl);
       }else{
           tbl += '<tr align="center">';
           tbl += '    <td colspan="5">Data not found </td>';
           tbl += '</tr>';

           $('.data-box tbody').html(tbl);
       }
              
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
                    url		: '{{ route("delete_addon_room") }}',
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

   function edit_data(id){
       $.ajax({
           type 	: 'POST',
           url		: '{{route("search_addon_room")}}',
           headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
           dataType: 'JSON',
           data 	: {
               'id' : id},
           success : function(msg) {
               data = msg.rs_data.data;
               $('#modal .modal-title').text('Edit data');
               $('#modal #form').attr('action','{{ route("edit_addon_room") }}');
               $('#modal select').prop("disabled", false);
               $('#modal input').prop("readonly", false);

               $.each(data,function(x,y){
                   $('#form  input[name="nama"]').val(y.nama);
                   $('#form  select[name="status"]').val(y.status);
                   $('#form  input[name="id"]').val(y.id);
               });
           
               $('#modal').modal('show');

           },
           error 	: function(xhr) {
               read_error(xhr);
           }
       });
   }

   $('#form').submit(function(e){
        e.preventDefault(e);
        var status = $('#status').val();
        var nama = $('#nama').val();
        if (nama.length <= 0 ) {
            notifMessage('Please input add-on name first ! ', 'warning');
            return;
        }

        if ( nama.length > 50 ) {
            notifMessage('Max. 50 Character for corporate code field ! ', 'warning');
            return;
        }
        
        if (status.length <= 0 ) {
            notifMessage('Please set status add-on first ! ', 'warning');
            return;
        }

        this.submit();
    });
    // end on submit

   </script>
@endpush