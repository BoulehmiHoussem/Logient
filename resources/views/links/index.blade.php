@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header">{{ __('My Shortcuts') }} <a class="btn btn-primary btn-sm float-end" href="{{ route('link.create') }}">{{ __('Add Shortcut') }}</a></div>
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
                                <p> {{ __('Shortcut For') }} : {{ $link->link }} </p>
                                </div>
                                <div class="col-lg-4">
                                    <form action="{{route('link.destroy', ['link' => $link->id])}}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-danger float-end btn-sm">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>   
                                </div>
                            </div>
                             
                        </li>
                            
                        @empty
                            <li class="list-group-item bg-warning">{{ __('No Shortcuts') }}</li>
                        @endforelse
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
