@extends('Layouts.Main')


@section('content')
<div class="main min-vh-100">

    <h1> HI </h1>

    @component('Components.alert',array('color'=>"red"))
        @slot('title')
    COMPONENT #1
    @endslot
        <strong>Whoops!</strong> Something went wrong!
    @endcomponent

    @markdownFile(file=app/Store/hello.md)

    <?php

    use Framework\Classes\Blade;

    $name = "Test Name";

    Blade::getInstance()->dump(['arg' => ['hi']], true);
    ?>

    {{$name | strtoupper | substr:0,5 }}

</div>
@endsection