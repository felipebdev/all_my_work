@extends('emails.base-template')
@push('after-styles')
    <style>
        .content a{
            color: #C4CF00; text-decoration: none
        }
    </style>
@endpush
@section('header')
    <h1 class="header h1" style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans',sans-serif; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;">
        {{ $subject ?? '' }}
    </h1>
@endsection
@section('content')
    <div class="content">
        {!! $message !!}
    </div>
@endsection
