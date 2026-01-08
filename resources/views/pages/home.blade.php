@extends('layouts.app')

@section('title', 'الصفحة الرئيسية')

@section('content')
    <!-- Header -->
    @include('components.layout.header')

    <!-- Hero Section -->
    @include('components.layout.hero')

    @include('components.layout.category')

    <!-- Books Section -->
    @include('components.layout.books-section')

    <!-- Authors Section -->
    @include('components.layout.authors-section')

    <!-- Footer -->
    @include('components.layout.footer')
@endsection