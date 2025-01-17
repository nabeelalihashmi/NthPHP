@extends('Layouts.Auth')

@section('page_title', 'Verification')
@section('content')
<div>
<h1 class="{{$success ? 'text-success' : 'text-danger'}} text-center">
    {{ $message }}
</h1>
<hr>
<div class="my-2 text-center">
   Go to <a href="{{BASEURL}}/login"> Login Page </a>
</div>
</div>
@endsection