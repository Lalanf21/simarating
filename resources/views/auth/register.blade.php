<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- loader-->
  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>

  <!--plugins-->
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
  {{-- select2 --}}
  <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />

   <!-- CSS Files -->
   <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
   <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

   <style>
     @media only screen and (min-width: 768px) {
      .card-register{
        margin-top: 150px
      }
    }
   </style>
  <title>Register - Pradita Partner Lounge</title>
</head>

<body class="bg-white">
  <div class="d-md-none d-lg-none d-xl-none login-bg-overlay"></div>

  <!--start wrapper-->
  <div class="wrapper">
    <div class="row g-0 m-0">
      <div class="col-xl-6 col-lg-12">
        <div class="login-cover-wrapper card-register">
          <div class="card shadow-none" >
            <div class="card-body">
              <img src="{{ asset('assets/images/logo_universitas.png') }}" alt="" class="img img-fluid" style="margin-top: 70px">
              <div class="text-center">
                <h4>Pradita Partner Lounge</h4>
                <p>Please fill in your personal data to continue</p>
              </div>
              <form class="form-body g-3" action="{{ route('proses_register') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                  <div class="col-lg-12">
                    <label for="nama" class="form-label">Name</label>
                    <input type="text" name="nama" class="form-control mb-3 @error('nama') is-invalid @enderror" id="nama">
                    @error('nama')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <label for="nomor_hp" class="form-label">Mobile number</label>
                    <input type="text" class="form-control mb-3 @error('no_hp') is-invalid @enderror" id="nomor_hp" name="no_hp">
                    @error('no_hp')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control mb-3 @error('email') is-invalid @enderror" id="email" name="email" >
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <label for="inputPassword" class="form-label">Password</label>
                    <input type="password" class="form-control mb-3  @error('password') is-invalid @enderror" id="inputPassword" name="password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <label for="inputPassword" class="form-label">Confirmation Password</label>
                    <input type="password" class="form-control mb-3 @error('password') is-invalid @enderror" id="inputPassword" name="password_confirmation">
                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <select name="asal_perusahaan" id="asal_perusahaan" class="form-select mb-3 asal_perusahaan  @error('asal_perusahaan') is-invalid @enderror">
                    </select>
                    @error('asal_perusahaan')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 mt-3">
                    <label for="corporate_code">Corporate code</label>
                    <input type="text" class="form-control mb-3" id="corporate_code" name="corporate_code">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" name="syaratKetentuan" type="checkbox" value="1" id="syaratKetentuan">
                      <label class="form-check-label" for="syaratKetentuan">
                        By proceeding, you agree with Pradita Partner Lounge’s 
                        <a href="" id="syaratModal">
                          Privacy Policy
                        </a>
                        @error('syaratKetentuan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-12">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                  </div>
                </div>
                <div class="col-12 col-lg-12 text-center">
                  <p class="mb-0">Already have an account ? <a href="{{ route('login') }}">Login</a></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-lg-12">
        <div class="position-fixed top-0 h-100 d-xl-block d-none login-cover-img">
        </div>
      </div>
    </div>
    <!--end row-->
  </div>
  <!--end wrapper-->

  {{-- modal S&K --}}
<div class="modal fade" id="modalSnK" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Privacy Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda ipsum sapiente suscipit nam, nobis debitis ad recusandae, voluptate obcaecati magni laboriosam voluptatibus voluptatum aliquam ab facilis dicta tenetur quas architecto aperiam corporis unde! Facilis minima earum ullam quo, praesentium consequuntur, ut impedit labore corrupti incidunt doloribus doloremque sit modi iste itaque nemo eum? Rem molestias obcaecati praesentium maxime ipsum amet id animi. Nihil debitis rerum voluptas, quidem cumque possimus perspiciatis amet expedita nemo deleniti incidunt? Deleniti quod vero nostrum magni quos ducimus nulla ratione alias odio doloremque. Laudantium unde sint blanditiis hic? Quod, temporibus sed dolorum pariatur necessitatibus at facilis placeat veniam molestias quam. Iure ex dolorem neque aut corrupti unde iste. Deserunt at sint, ducimus in facere officia magni officiis vel asperiores commodi dolores corporis perferendis doloremque eaque, cupiditate recusandae dolore eius nesciunt debitis magnam, odit laboriosam rerum numquam! Facilis veritatis sed a facere deleniti unde assumenda dolorem, quia ex suscipit, sint delectus. Error id sapiente eveniet perferendis, sint quis cupiditate quo quos quod ipsa enim explicabo dignissimos! Necessitatibus, facere architecto perspiciatis vitae cupiditate ducimus corporis? Eum, corporis? Magni dolor incidunt accusantium iure labore ipsum animi velit repellendus architecto minus possimus molestiae eos adipisci aut, dolorum tempora. Excepturi quaerat sit vel iusto, dolor qui temporibus magnam quod voluptates nostrum eum labore, mollitia dolore impedit natus! Voluptatem deleniti porro rem quos corporis fugiat nostrum repellat ut? Tenetur porro optio, cupiditate voluptatibus consectetur eos tempora facilis necessitatibus! Odio, nihil ex! Labore dolor mollitia maiores quisquam assumenda commodi itaque qui voluptate at! Tempora, ad hic. Laudantium asperiores obcaecati dolore veniam quia vel ipsum aliquid iure facere quibusdam dicta sequi repellendus dolores eveniet, iusto a possimus libero! Quidem, est tempora earum consequuntur in natus itaque eaque vel temporibus quisquam similique, repellat alias, sit impedit ea eum debitis? Enim ut blanditiis ipsam et explicabo laborum doloremque. Quos recusandae ullam repellendus excepturi expedita explicabo iste. Commodi molestiae mollitia quia quis! Soluta explicabo assumenda, vitae molestias sequi fugiat placeat maiores deserunt nulla voluptates officia minus quos quasi maxime quidem neque! Blanditiis delectus corporis saepe numquam nulla possimus, tempore accusantium, repudiandae maxime voluptate provident. Quisquam minima distinctio cumque voluptatum vel aut similique necessitatibus! Ducimus cum architecto sit delectus iusto veniam ut est tenetur, quidem corrupti, explicabo, voluptatum voluptatem fugit distinctio incidunt earum laudantium qui debitis officiis eius asperiores? Voluptatem, distinctio debitis cumque quidem amet possimus dicta accusantium necessitatibus modi maiores dolore esse natus suscipit provident quam eligendi dolores consequatur aperiam corrupti aliquam iusto rerum porro ipsam assumenda? Impedit, aut tempore. Id, minima temporibus. Cum vitae reprehenderit ad facilis laudantium vel optio, blanditiis quisquam soluta ratione officiis et eum? Illo, blanditiis odit accusantium minus laborum tenetur vero, esse, aspernatur adipisci expedita earum magni. Dolorem eos repellendus saepe labore cumque praesentium illo tempore qui culpa, magni adipisci veritatis corrupti minus sequi pariatur inventore, est nam porro earum voluptate facilis totam. Ipsa accusantium, doloremque provident at, fugiat deserunt id odit nulla non sunt rerum labore accusamus voluptatem aut, nam voluptate facilis? Rem nisi alias obcaecati, eaque provident ab vitae asperiores!
      </div>   
    </div>
  </div>
</div>
{{-- akhir modal S&K --}}
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
<script>
    @if(Session::has('alert_message'))
      Swal.fire({
        icon: "{{ Session::get('alert_type') }}",
        text: "{{ Session::get('alert_message') }}"
      });
    @endif

    $('#syaratModal').click(function(e){
      e.preventDefault();
      $('#modalSnK').modal('show');
    });

    function formatState (opt) {
      if (!opt.id) {
          return opt.text.toUpperCase();
      } 

      var optimage = $(opt.element).attr('data-logo'); 
      var pathLogo = '{{ asset("upload/img/mitra/thumbnail") }}';
      if(!optimage){
        return opt.text;
      } else {                    
          var $opt = $(
            '<span><img src="' + pathLogo+ '/' + optimage + '" width="60px" /> ' + opt.text + '</span>'
          );
          return $opt;
      }
    };

    $('.asal_perusahaan').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: 'Company origin',
        templateResult: formatState,
        templateSelection: formatState,
    });

    // set autofocus search select2 
    $(document).on('select2:open', () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    // get data mitra
    $(document).ready( () => {
      // ajax
      $.ajax({
          type 	: 'POST',
          url		: '{{route("get_mitra_perusahaan")}}',
          headers	: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
          dataType: 'JSON',
          success : function(rst) {
            opt = '<option value="">Perusahaan</option>';
            if (rst.length > 0) {
                $.each(rst,function(x,y){
                    opt += '<option value="'+ y.id +'" data-logo="'+ y.logo +'" >'+ y.nama_brand +'</option>';
                });
                $('#asal_perusahaan').html(opt);
            }
          },
          error 	: function(xhr) {
              console.log(xhr);
          },
          
      });
      // end ajax
    });

   
</script>
</body>

</html>