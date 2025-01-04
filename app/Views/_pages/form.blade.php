@extends('layouts.main')

@section('title', 'Home - NthPHP')

@section('content')
    <form action="{{cfg('app.base_url')}}/submit" method="post">
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="email" placeholder="Email">
        <button type="submit">Submit</button>
    </form>
@endsection
