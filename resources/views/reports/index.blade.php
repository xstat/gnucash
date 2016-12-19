@extends('layouts.app')

@section('title', 'Profit vs. Loss')

@section('content')

    @section('sectionTitle', 'Reports')
    @section('sectionContent')

        <ul>
            @foreach ($reports as $reportId => $reportSettings)
                <li><a href="{{ route('reportView', $reportId) }}">
                    {{ $reportSettings['title'] }}
                </a></li>
            @endforeach
        </ul>

    @endsection

    @include('layouts.partials.section')

@endsection
