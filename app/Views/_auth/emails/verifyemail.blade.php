@extends('Layouts.Email')

@section('content')
    @section('title', 'Verify Your Account!')

    <p>
        We have received your request to join NthPHP.
        Please click on this link to verify
    </p>
    <p>

        <a href="{{ $url }}/verify/{{$selector}}/{{$token}}">verify</a>
    </p>

    <div>
        or copy this url
        <textarea name="" readonly=true id="" style="width: 100%" rows="3">
        {{ $url }}/verify/{{$selector}}/{{$token}}        
        </textarea>
    </div>
@endsection