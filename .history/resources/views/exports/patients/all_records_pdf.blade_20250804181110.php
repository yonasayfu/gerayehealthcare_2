@extends('print-layout')

@section('content')
    <x-printable-report
        :title="$document_title ?? 'Patient List (All Records)'"
        :data="$items"
        :columns="$columns"
        :header-info="[]" {{-- You might need to pass actual header info from config or controller --}}
        :footer-info="[]" {{-- You might need to pass actual footer info from config or controller --}}
    />
@endsection
