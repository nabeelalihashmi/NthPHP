@extends('Layouts.Main')
@section('page_title', 'Message')

@section('content')


<style>
    .mx-400 {
        max-width: 400px;
    }

    .f_wrapper {
        height: calc(100vh - 100px);
    }

    .w-90 {
        width: 90%;
    }
</style>


<div class="f_wrapper d-flex flex-column justify-content-center align-items-center">
    <div class="w-90 mx-400 mx-auto ">
        <h1 class="display-6 mb-3 w-100">{{$heading}}</h1>
        <p class="lead">{!!$message!!}</p>
    </div>
</div>

@endsection