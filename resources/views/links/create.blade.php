@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-3">
            <form action="{{ route('link.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <input type="text" value="{{ old('link') }}" name="link" class="form-control @error('link') is-invalid @enderror" placeholder="{{ __('trans.Put link here ...') }}" >
                        @error('link')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ __($message) }}</strong>
                            </span>
                        @enderror            
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary">
                            {{ __('trans.Submit') }}
                        </button>
                        <a href="{{ route('link.index') }}" class="btn btn-warning">
                            {{ __('trans.Cancel') }}
                        </a>
                    </div>

                </div>
                </form>

        </div>
    </div>
</div>
@endsection
