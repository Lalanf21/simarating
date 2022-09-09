@extends('layout.master')
@section('title','Setting users')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Setting users</h4>
        <hr>
    </div>
</div>

<div class="d-md-flex flex-row-reverse mb-2">
    <a href="" id="buttonAdd" type="button" class="btn m-2 ms-2 btn-sm btn-outline-primary" data-bs-toggle="modal" >
        <i class="fas fa-plus"></i> Add new
    </a>
    <div class="input-group m-2 input-group-sm search-box" style="max-width: 250px;">
        <input name="" id="text-search" type="text" class="form-control" placeholder="">
        <div class="input-group-append ms-2">
            <span class="input-group-text" id="search-addon"><i class="fa fa-search"></i></span>
        </div>
    </div>
    <select name="id" id="levelSearch" class="form-select m-2 form-select-sm me-3" style="max-width: 180px;">
        <option value=" "> Level </option>
        <option value="1"> Admin </option>
        <option value="2"> Pengguna </option>
    </select>
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
                                <th scope="col">Email</th>
                                <th scope="col">Mobile number</th>
                                <th scope="col">Level</th>
                                <th scope="col">status</th>
                                <th scope="col">Option</th>
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
            <form action="" method="POST" id="form">
                @csrf
                @method('POST')
                
                <input type="hidden" name="id">
                <label for="nama">Name</label>
                <input type="text" name="nama" id="nama" class="form-control mb-3" required autofocus>

                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control mb-3" required>
               
                <label for="no_hp"> Mobile number </label>
                <input type="text" name="no_hp" id="no_hp" class="form-control mb-3" required>

                <label for="level">Level </label>
                <select name="level" id="level" class="form-select mb-3">
                    <option value="">-- Pilih --</option>
                    <option value="1"> Admin </option>
                    <option value="2"> Pengguna </option>
                </select>

                <label for="status">status </label>
                <select name="status" id="status" class="form-select mb-3">
                    <option value="">-- Pilih --</option>
                    <option value="1"> Active </option>
                    <option value="0"> Non-active </option>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    var max_row = {{$max_row}};
   var order_search = 0;
   var sort_search = 'asc';

   $(document).ready(function(){
       search_process();
       $('#buttonAdd').click(function(){
           $('#modal .modal-title').text('Insert data');
           $('#modal #form').attr('action','{{ route("add_users") }}');
           $('#modal select').prop("disabled", false);
           $('#modal input').prop("readonly", false);
           $('#form  input[name="nama"]').val('');
           $('#form  select[name="status"]').val('');
           $('#form  select[name="level"]').val('');
           $('#form  input[name="email"]').val('');
           $('#form  input[name="nik"]').val('');
           $('#modal').modal('show');
       });

       $("form").submit(function(e) {
           e.preventDefault();
       });
       
       $("#text-search, #levelSearch").change(function(){
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
        levelSearch = $.trim($("#levelSearch").val());

       $.ajax({
           type 	: 'POST',
           url		: '{{route("search_users")}}',
           headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
           dataType: 'JSON',
           data 	: {
               'page'  :   search_page,
               'sort_search'   : sort_search,
               'order_search'  : order_search,
               'text_search' : text_search,
               'levelSearch' : levelSearch,
           },
           success : function(msg) {
               var rs = msg.rs_data;
               var dt = rs["data"];
               show_data(dt,1);
               // console.log(dt);

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
           tbl += '    <td>'+y.email+'</td>';
           tbl += '    <td>'+y.no_hp+'</td>';
           if (y.level == '1') {
               tbl += '    <td> Admin </td>';
            }else{
               tbl += '    <td> Pengguna </td>';
           }
           if (y.status == '1') {
               tbl += '    <td> <i class="fas fa-check"></i> Aktif</td>';
            }else{
               tbl += '    <td> <i class="fas fa-times"></i> Tidak aktif</td>';
           }
           tbl += '    <td align="right">';
           tbl += '        <div class="btn-group" role="group" aria-label="Button Control">';
           tbl += '             <button type="button" class="btn-edit btn btn-sm btn-light text-info" onclick="edit_data(\''+y.id+'\')"><i class="fas fa-pencil-alt"></i></button>';
           tbl += '             <button type="button" class="btn-delete btn btn-sm btn-light text-danger" onclick="delete_process(\''+y.email+'\')"><i class="fas fa-trash-alt"></i></button>';
           tbl += '        </div>';
           tbl += '    </td>';
           tbl += '</tr>';

           });

           $('.data-box tbody').html(tbl);
       }else{
           tbl += '<tr align="center">';
           tbl += '    <td colspan="7">Data not found</td>';
           tbl += '</tr>';

           $('.data-box tbody').html(tbl);
       }
              
   }

   function delete_process(email){
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
                    url		: '{{ route("delete_users") }}',
                    headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    dataType: 'JSON',
                    data 	: {
                        'email': email,

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
           url		: '{{route("search_users")}}',
           headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
           dataType: 'JSON',
           data 	: {
               'id' : id},
           success : function(msg) {
               data = msg.rs_data.data;
               // console.log(data);return;
               $('#modal .modal-title').text('Edit data');
               $('#modal #form').attr('action','{{ route("edit_users") }}');

               $.each(data,function(x,y){
                   $('#form  input[name="nama"]').val(y.nama);
                   $('#form  input[name="email"]').val(y.email);
                   $('#form  input[name="no_hp"]').val(y.no_hp);
                   $('#form  select[name="level"]').val(y.level);
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
        e.preventDefault();
        var nama = $('#nama').val();
        var email = $('#email').val();
        var no_hp = $('#no_hp').val();
        var level = $('#level').val();
        var status = $('#status').val();
        // alert( !isNaN( parseInt(no_hp)) );return;
        if ( nama.length <= 0 ) {
            notifMessage('Please input name first ! ', 'warning');
            return;
        }
        
        if ( email.length <= 0 ) {
            notifMessage('Please input email first ! ', 'warning');
            return;
        }
        
        if ( no_hp.length <= 0 ) {
            notifMessage('Please input mobile number first ! ', 'warning');
            return;
        }

        if ( no_hp.length >= 15 ) {
            notifMessage('Max. 15 character for mobile numver field ! ', 'warning');
            return;
        }

        if (isNaN( parseInt(no_hp)) ) {
            notifMessage('Only number allowed for mobile number field ! ', 'warning');
            return;
        }

        if ( level.length <= 0 ) {
            notifMessage('Please select level first ! ', 'warning');
            return;
        }

        if ( status.length <= 0 ) {
            notifMessage('Please select status first ! ', 'warning');
            return;
        }


        this.submit();
    });
    // end on submit


   </script>
@endpush