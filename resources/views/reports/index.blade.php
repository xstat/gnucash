@extends('layouts.app')

@section('title', 'Profit vs. Loss')

@section('content')

    @section('section.title', 'Reports')
    @section('section.content')

        <ul>
            @foreach ($reports as $reportId => $reportSettings)
                <li><a href="{{ route('reportView', $reportId) }}">
                    {{ $reportSettings['title'] }}
                </a></li>
            @endforeach
        </ul>

    @endsection

    @include('partials.section')

@endsection
