@extends('layout.master')
@section('title','Company partner')
@section('content')

@push('after-style')
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
@endpush
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Company partner</h4>
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
                                <th scope="col">Company</th>
                                <th scope="col">Brand</th>
                                <th scope="col">PIC</th>
                                <th scope="col">Phone number</th>
                                <th scope="col">Corporate code</th>
                                <th scope="col">Credit</th>
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
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="form"  action="" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <label for="nama">Company</label>
                <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" id="nama_perusahaan" class="form-control mb-3" required>
                <input type="hidden" name="id">

                <label for="nama">Brand</label>
                <input type="text" name="nama_brand" value="{{ old('nama_brand') }}" id="nama_brand" class="form-control mb-3" required>

               <div class="form-group">
                    <label for="corporation_code">Corporate Code</label>
                    <input type="text" name="corporation_code" id="corporation_code" value="{{ old('corporation_code') }}" class="form-control" required >
                    <p class="text-muted">
                        <span >* Max. 10 Character</span>
                    </p>
               </div>

                <label for="kuota">Credit *</label>
                <input type="number" name="kuota" id="kuota" class="form-control" value="{{ old('kuota') }}" required>
                <p class="text-muted" style="margin-bottom: 25px">
                    <span >* In a month</span>
                </p>
                
                <div id="pic_input_group">
                    <label for="pic"> PIC </label>
                    <select name="pic" id="pic" class="form-select mb-3">
                    </select>
                </div>

                <label>Logo</label> <br>
                <img id="logo_image" class="img-thumbnail mb-2">
                <input type="file" name="logo" 
                    id="logo"
                    class="dropify" 
                    data-height="200"
                    data-max-file-size="1M">
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
   
    {{-- dropidy --}}
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>

    <script type="text/javascript">
    var max_row = {{$max_row}};
    var order_search = 0;
    var sort_search = 'asc';

    $(document).ready(function(){
        $('[data-bs-toggle="tooltip"]').tooltip();

        search_process();
        
        $('#buttonAdd').click(function(){
            $('#modal .modal-title').text('Insert data');
            $('#form  input[name="nama_perusahaan"]').val('');
            $('#form  input[name="nama_brand"]').val('');
            $('#form  input[name="corporation_code"]').val('');
            $('#form  input[name="kuota"]').val('');
            $('#form  input[name="logo"]').attr('data-default-file', '');
            $('#modal  input').prop("readonly", false);
            $('#form').attr('action', '{{ route('add-mitra') }}');
            $('#form #logo_image').attr('src', '');
            $('#form #pic_input_group').addClass('d-none');
            $('#modal').modal('show');
        });

        $('.dropify').dropify({
            messages: {
                default: 'Upload logo',
                replace: 'Change',
                remove:  'Delete',
                error: {
                    'fileSize': '1M maximal.',
                    'imageFormat': 'Invalid format.'
                }
            }
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
            url		: '{{route("search_mitra")}}',
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
            namaPic = '-';
            no_hp = '-';
            if (y.pic !== null) {
                namaPic = y.pic.nama;
                no_hp = y.pic.no_hp;
            }
            tbl += '<tr align="center">';
            tbl += '    <td>'+(page)+'</td>';
            tbl += '    <td>'+y.nama_perusahaan+'</td>';
            tbl += '    <td>'+y.nama_brand+'</td>';
            tbl += '    <td>'+namaPic+'</td>';
            tbl += '    <td>'+no_hp+'</td>';
            tbl += '    <td>'+y.corporation_code+'</td>';
            tbl += '    <td>'+y.kuota+'</td>';
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
            tbl += '    <td colspan="8">Data not found</td>';
            tbl += '</tr>';
            $('.data-box tbody').html(tbl);

        }
               
    }

    function delete_process(id){
        Swal.fire({
            title: "Are you sure ?",
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
                    url		: '{{ route("delete-mitra") }}',
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
            url		: '{{route("search_mitra")}}',
            headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            dataType: 'JSON',
            data 	: {
                'id' : id},
            success : function(msg) {
                // console.log(msg);return;
                data = msg.rs_data.data;
                
                mitra_user =  msg.mitra_user;
                urlLogo = '{!! asset("upload/img/mitra/thumbnail") !!}';
                $('#modal .modal-title').text('Edit data');
                $('#modal select').prop("disabled", false);
                $('#modal input').prop("readonly", false);
                $('#form #pic_input_group').removeClass('d-none');

                if (mitra_user.length < 1) {
                    $('#pic_input_group select[id="pic"]').prop('disabled', true);
                    $('#pic_input_group select[id="pic"]').val('');
                }

                $.each(data,function(x,y){
                    $('#form  input[name="nama_perusahaan"]').val(y.nama_perusahaan);
                    $('#form  input[name="nama_brand"]').val(y.nama_brand);
                    $('#form  input[name="kuota"]').val(y.kuota);
                    $('#form  input[name="corporation_code"]').val(y.corporation_code);
                    $('#form  input[name="id"]').val(y.id);
                    $('#form  input[id="pic"]').val(y.pic);
                    $('#form #logo_image').attr('src', urlLogo+'/'+y.logo);
                    $('#modal #form').attr('action','{{ route('edit-mitra') }}');
                });

                opt = '<option value="" selected disabled> Select </option>';
                $.each(mitra_user,function(x,y){
                    opt += '<option value="'+y.id+'">'+y.nama+'</option>';
                    $('#pic_input_group select[id="pic"]').html(opt);
                });

            
                $('#modal').modal('show');

            },
            error 	: function(xhr) {
                read_error(xhr);
            }
        });
    }

    // on submit
    $('#form').submit(function(e){
        e.preventDefault();
        var nama_brand = $('#nama_brand').val();
        var nama_perusahaan = $('#nama_perusahaan').val();
        var kuota = $('#kuota').val();
        var corporation_code = $('#corporation_code').val();
        var logo = $('#logo').val();
        var action = $('#form').attr('action');
        // console.log( parseInt(kuota) );return;
        if ( nama_perusahaan.length <= 0 ) {
            notifMessage('Please input company name first ! ', 'warning');
            return;
        }
        
        if ( nama_brand.length <= 0 ) {
            notifMessage('Please input company brand name first ! ', 'warning');
            return;
        }

        
        if ( corporation_code.length <= 0 ) {
            notifMessage('Please input corporate code first ! ', 'warning');
            return;
        }

        if ( corporation_code.length > 10 ) {
            notifMessage('Max. 10 Character for corporate code field ! ', 'warning');
            return;
        }

        if ( parseInt(kuota) > 999 ) {
            notifMessage('Max. numer for credit field is 999 ! ', 'warning');
            return;
        }
        
        if ( kuota.length <= 0 ) {
            notifMessage('Please input credit first ! ', 'warning');
            return;
        }

        if ( isNaN( parseInt(no_hp)) ) {
            notifMessage('Only number allowed for credit field ! ', 'warning');
            return;
        }

        if(action == '{{ route("add-mitra") }}'){
            if (logo.length <= 0 ) {
                notifMessage('Please upload logo ! ', 'warning');
                return;
            }
        }

        this.submit();
    });
    // end on submit

    </script>
@endpush