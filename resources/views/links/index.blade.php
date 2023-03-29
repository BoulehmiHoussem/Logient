@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-3">
            @if(Session::has('created'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    {{ Session::get('created') }}
                </div>
            </div>
            @endif

            @if(Session::has('deleted'))
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    {{ Session::get('deleted') }}
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">{{ __('trans.My Shortcuts') }} <a class="btn btn-primary btn-sm float-end" href="{{ route('link.create') }}">{{ __('trans.Add Shortcut') }}</a></div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($links as $link)
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-lg-2">
                                <span class="badge badge-info bg-info"> #{{ $link->id }} </span>
                                </div>
                                <div class="col-lg-6">
                                <a href="{{route('link.shortcut', ['shortcut' => $link->shortcut])}}" target="_blank"> {{route('link.shortcut', ['shortcut' => $link->shortcut])}}  </a>
                                <p> {{ __('trans.Shortcut For') }} : {{ $link->link }} </p>
                                </div>
                                <div class="col-lg-4">
                                    <form action="{{route('link.destroy', ['link' => $link->id])}}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-danger float-end btn-sm">
                                            {{ __('trans.Delete') }}
                                        </button>
                                    </form>   
                                </div>
                            </div>
                             
                        </li>
                            
                        @empty
                            <li class="list-group-item bg-warning">{{ __('trans.No Shortcuts') }}</li>
                        @endforelse
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
