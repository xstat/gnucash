@extends('layouts.app')

@section('title', 'Report Detail')

@section('content')

    @section('sectionTitle', $report->getTitle())
    @section('sectionContent')
        @include($report->getViewName())
    @endsection

    @include('layouts.partials.section')

@endsection
