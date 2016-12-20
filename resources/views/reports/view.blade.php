@extends('layouts.app')

@section('title', 'Report Detail')

@section('content')

    @section('section.title', $report->getTitle())
    @section('section.content', $report->getVueComponentName())
    @include('partials.section')

@endsection
