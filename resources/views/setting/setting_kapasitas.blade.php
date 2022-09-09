@extends('layout.master')
@section('title','Capacity setting')
@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Capacity setting</h4>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <form action="{{ route('add_kapasitas') }}" method="post">
                            @csrf
                            @method('POST')
                            <label for="kapasitas">
                                Capacity
                            </label>
                            <input type="hidden" name="id" value="{{ ($kapasitas) ? $id : '1' }}">
                            <input type="number" name="kapasitas" id="kapasitas" class="form-control mb-3" value="{{ $kapasitas }}">

                            <button type="submit" class="btn btn-outline-success">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after-script')
    
@endpush