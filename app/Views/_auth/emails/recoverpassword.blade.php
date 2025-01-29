@extends('Layouts.Email')

@section('content')
    @section('title', 'Recover or Update Password!')

    <p>
        We have received your request to recover/update your password for account at NthPHP.
        Please click on this link to initiate recovery
    </p>
    <p>
        <a href="{{ $url }}/reset/{{$selector}}/{{$token}}">Recover</a>
    </p>

    <div>
        or copy this url
        <textarea name="" readonly=true id="" style="width: 100%" rows="3">
        {{ $url }}/reset/{{$selector}}/{{$token}}        
        </textarea>
    </div>
@endsection