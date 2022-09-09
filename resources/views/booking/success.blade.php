@extends('layout.master')
@section('title','Success reservation')
@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-footer">
                <h4 class="text-center"> {{ $qrCode }} </h4>
            </div>
            <div class="card-header">
                <div class="container">
                    <div class="row p-3">
                        <div class="col-md-6">
                            <strong><p>Company name</p></strong>
                            <p> {{ $header->nama_perusahaan }} </p>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <strong><p>Reservation date</p></strong>
                                <p>{{ date_en_full($header->booking_date) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row p-3">
                        <div class="col-md-6">
                            <strong><p>Name</p></strong>
                            @foreach ($userInvited as $user)
                                <p>{{ $loop->iteration.'. '. $user->nama_mitra }}</p>
                            @endforeach
                        </div>
                        @php
                            if ($header->end_time == '13:00'){
                                $shiftLabel = '08:00-13:00';
                            }else{
                                $shiftLabel = '13:00-18:00';
                            }
                        @endphp
                        <div class="col-md-6">
                            <strong><p>Number of seat</p></strong>
                            <p>{{ count($userInvited).' Seat, '.$shiftLabel }}</p>
                        </div>
                    </div>

                    <div class="row p-3">
                        <div class="col-md-6">
                            <strong><p>Add-on</p></strong>
                            @forelse ($addons as $addon)
                                <p>{{ $addon->nama_addon }} - {{ $addon->start_time.'-'.$addon->end_time }} </p>
                            @empty
                                <p>No add-on</p>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            <a href=""  data-bs-toggle="modal" data-bs-target="#modalQrCode">
                                <img src="{{asset('/storage/img/qr-codes/'. $qrCode .'/qrcode.png') }}">
                            </a>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary me-3">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>

<!-- modalQrCode -->
<div id="modalQrCode" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img width="100%" src="{{asset('/storage/img/qr-codes/'. $qrCode .'/qrcode.png') }}" />
                <h5 class="text-center">{{ $qrCode }}</h5>
            </div>
        </div>
    </div>
</div>
<!-- end modalQrCode -->
@endsection

@push('after-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
<script>
    @if(Session::has('booking'))
        Swal.fire({
            title: "{{ Session::get('booking') }}",
            icon: "success",
            text: "Please check your email for details"
        });
    @endif
</script>
@endpush