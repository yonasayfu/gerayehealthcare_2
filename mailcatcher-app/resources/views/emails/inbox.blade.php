@extends('layouts.app')

@section('content')
    <h1 class="my-4">Inbox</h1>

    <div class="list-group">
        @forelse ($emails as $email)
            <a href="{{ route('emails.show', $email) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $email->subject }}</h5>
                    <small>{{ $email->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">From: {{ $email->from[0]['name'] ?? $email->from[0]['address'] }}</p>
                <p class="mb-1">To: {{ collect($email->to)->map(fn($i) => $i['name'] ?? $i['address'])->implode(', ') }}</p>
            </a>
        @empty
            <div class="list-group-item">
                <p class="mb-1 text-center">No emails yet.</p>
            </div>
        @endforelse
    </div>
@endsection
