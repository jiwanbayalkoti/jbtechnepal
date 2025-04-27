@extends('layouts.app')

@section('meta_title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? '')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                @if($page->featured_image)
                <img src="{{ asset('storage/' . $page->featured_image) }}" class="card-img-top" alt="{{ $page->title }}" style="max-height: 400px; object-fit: cover;">
                @endif
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4">{{ $page->title }}</h1>
                    <div class="card-text">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 