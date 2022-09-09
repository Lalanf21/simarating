@extends('layout.master')
@section('title','Profile')
@section('content')
<div class="container">
    <div class="card">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card overflow-hidden radius-10">
                    <div class="profile-cover bg-dark position-relative mb-4">
                        <div class="user-profile-avatar shadow position-absolute top-50 start-0 translate-middle-x">
                            <img src="{{ asset('assets/images/avatars/user.jpg') }}">
                        </div>
                    </div>
                    <div class="card-body bg-dark text-white">
                        <div class="mt-5 d-flex align-items-start justify-content-between">
                            <div class="">
                                <h3 class="mb-2">
                                    {{ ($user)?$user->nama:'admin' }}
                                </h3>
                                <p class="mb-1">{{ ($user)?$user->nama_perusahaan:'PT. summarecon' }}</p>
                                <p class="mb-1">{{ ($user)?$user->email:'admin@admin.com' }}</p>
                                <p>{{ ($user)?$user->no_hp:'' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="card ">
        <div class="card-header">
            <h4 class="card-title">Change password</h4>
            <hr>
        </div>
        <div class="card-body">
            <div class="row d-flex justify-content-center">
                <div class="col-md-6">
                    <form action="{{ route('proses_ubah_password') }}" method="post">
                        @csrf
                        @method('POST')
                        <label for="passLama">
                            Current password
                        </label>
                        <input type="password" class="form-control mb-3  @error('passLama') is-invalid @enderror"
                            name="passLama">
                        @error('passLama')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <label for="password">
                            New password
                        </label>
                        <input type="password" class="form-control mb-3  @error('password') is-invalid @enderror"
                            name="password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <label for="password">
                            Confirm your password
                        </label>
                        <input type="password"
                            class="form-control mb-3  @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <button type="submit" class="btn btn-sm btn-info text-light">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection