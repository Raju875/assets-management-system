@extends("email.email-template")

@section('title')
    {!! $header !!}
@endsection

@section("content")
    {!! $param !!}
@endsection
