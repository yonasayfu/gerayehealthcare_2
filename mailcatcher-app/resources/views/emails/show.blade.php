@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1>{{ $email->subject }}</h1>
        <a href="{{ route('inbox') }}" class="btn btn-primary">Back to Inbox</a>
    </div>

    <div class="card">
        <div class="card-header">
            <p><strong>From:</strong> {{ $email->from[0]['name'] ?? $email->from[0]['address'] }}</p>
            <p><strong>To:</strong> {{ collect($email->to)->map(fn($i) => $i['name'] ?? $i['address'])->implode(', ') }}</p>
            @if($email->cc)
                <p><strong>CC:</strong> {{ collect($email->cc)->map(fn($i) => $i['name'] ?? $i['address'])->implode(', ') }}</p>
            @endif
            @if($email->bcc)
                <p><strong>BCC:</strong> {{ collect($email->bcc)->map(fn($i) => $i['name'] ?? $i['address'])->implode(', ') }}</p>
            @endif
            <p><strong>Received:</strong> {{ $email->created_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html" type="button" role="tab" aria-controls="html" aria-selected="true">HTML</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="text-tab" data-bs-toggle="tab" data-bs-target="#text" type="button" role="tab" aria-controls="text" aria-selected="false">Text</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="raw-tab" data-bs-toggle="tab" data-bs-target="#raw" type="button" role="tab" aria-controls="raw" aria-selected="false">Raw</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="html" role="tabpanel" aria-labelledby="html-tab">
                    @if($email->html_body)
                        <iframe srcdoc="{{ $email->html_body }}" class="w-100" style="height: 60vh; border: 1px solid #ccc;"></iframe>
                    @else
                        <p class="p-3">No HTML body.</p>
                    @endif
                </div>
                <div class="tab-pane fade" id="text" role="tabpanel" aria-labelledby="text-tab">
                    @if($email->text_body)
                        <pre class="p-3">{{ $email->text_body }}</pre>
                    @else
                        <p class="p-3">No text body.</p>
                    @endif
                </div>
                <div class="tab-pane fade" id="raw" role="tabpanel" aria-labelledby="raw-tab">
                    <pre class="p-3">{{ $email->raw_source }}</pre>
                </div>
            </div>
        </div>
    </div>
@endsection
