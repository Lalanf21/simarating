@extends('layout.master')
@section('title','Konfirmasi reservasi')
@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-footer d-flex justify-content-between">
                <div class="col-3 ">
                    <a href="{{ route('add_schedule') }}" class="btn btn-info me-3">
                        <i class="fas fa-arrow-left text-white"></i>
                    </a>
                </div>
                <div class="col-9">
                    <h4> Your confirmation reservation </h4>
                </div>
            </div>
            <div class="card-header">
                <div class="container">
                    <div class="row p-3">
                        <div class="col-md-6">
                            <strong><p>Reservation date</p></strong>
                            <p>{{ date_en_full( Session::get('data_booking')['tanggal'] ) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><p>Number of seat</p></strong>
                            <p>{{ Session::get('data_booking')['tanggal'].' Seat' }}</p>
                        </div>
                    </div>
         
                    <div class="row p-3">
                        <div class="col-md-6">
                            <strong><p>Name</p></strong>
                            @foreach (Session::get('data_booking')['user_invite'] as $user)
                                <p>{{ $user['nama_user'] }}</p>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <strong><p> Shift </p></strong>
                           @if (Session::get('data_booking')['shift'] == 1)
                                Morning 08:00 - 13:00
                                @else
                                Afternoon 13:00 - 18:00
                           @endif
                        </div>
                    </div>

                    <div class="row p-3">
                        <div class="col-md-12">
                            <strong><p>Add-on</p></strong>
                            @forelse (Session::get('data_booking')['addons'] as $addon)
                                <p>{{ $addon['nama_addon'] }} - {{ $addon['start_time'].'-'.$addon['end_time'] }} </p>
                            @empty
                                <p>No add-on</p>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-center">
                            <form action="{{ route('proses-add-schedule') }}" method="post">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="shift" value="{{ Session::get('data_booking')['shift'] }}">
                                <input type="hidden" name="id_mitra" value="{{ Session::get('data_booking')['id_mitra'] }}">
                                <input type="hidden" name="isAddon" value="{{ Session::get('data_booking')['isAddon'] }}">
                                <input type="hidden" name="booking_date" value="{{ Session::get('data_booking')['tanggal'] }}">
                                <input type="hidden" name="user_invited" value="{{ serialize(Session::get('data_booking')['user_invite']) }}">
                                <input type="hidden" name="addons" value="{{ serialize(Session::get('data_booking')['addons']) }}">
                                <button type="submit" class="btn btn-success btn-sm">
                                    Confirm <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
@endsection