@extends('layouts.app')

@section('title', $page->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title h2 mb-4">{{ $page->name }}</h1>
                    
                    <div class="page-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
